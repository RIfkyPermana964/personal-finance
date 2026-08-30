<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSavingGoalRequest;
use App\Http\Requests\StoreSavingMutationRequest;
use App\Models\PaymentMethod;
use App\Models\SavingGoal;
use App\Services\SavingGoalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SavingGoalController extends Controller
{
    public function __construct(protected SavingGoalService $savingGoalService) {}

    public function index(Request $request): View
    {
        $userId = $request->user()->id;
        $goals = $this->savingGoalService->getGoals($userId);
        $paymentMethods = PaymentMethod::forUser($userId)->where('is_active', true)->get();

        $totalSaved = $goals->sum('current_amount');
        $totalTarget = $goals->where('status', '!=', 'cancelled')->sum('target_amount');

        return view('saving-goals.index', compact('goals', 'paymentMethods', 'totalSaved', 'totalTarget'));
    }

    public function store(StoreSavingGoalRequest $request): RedirectResponse
    {
        $this->savingGoalService->createGoal($request->user()->id, $request->validated());

        return redirect()->route('saving-goals.index')->with('success', 'Target tabungan baru berhasil dibuat!');
    }

    public function update(StoreSavingGoalRequest $request, SavingGoal $savingGoal): RedirectResponse
    {
        abort_if($savingGoal->user_id !== $request->user()->id, 403);

        $this->savingGoalService->updateGoal($savingGoal, $request->validated());

        return redirect()->route('saving-goals.index')->with('success', 'Target tabungan berhasil diperbarui!');
    }

    public function mutate(StoreSavingMutationRequest $request, SavingGoal $savingGoal): RedirectResponse
    {
        abort_if($savingGoal->user_id !== $request->user()->id, 403);

        $data = $request->validated();
        if ($data['type'] === 'deposit') {
            $this->savingGoalService->deposit(
                $savingGoal,
                (float) $data['amount'],
                $data['payment_method_id'] ?? null,
                $data['transaction_date'],
                $data['notes'] ?? null
            );
            $msg = 'Setoran tabungan berhasil dicatat!';
        } else {
            if ((float) $data['amount'] > (float) $savingGoal->current_amount) {
                return back()->withErrors(['amount' => 'Nominal penarikan melebihi saldo tabungan saat ini.']);
            }
            $this->savingGoalService->withdraw(
                $savingGoal,
                (float) $data['amount'],
                $data['payment_method_id'] ?? null,
                $data['transaction_date'],
                $data['notes'] ?? null
            );
            $msg = 'Penarikan dana tabungan berhasil dicatat!';
        }

        return redirect()->route('saving-goals.index')->with('success', $msg);
    }

    public function destroy(Request $request, SavingGoal $savingGoal): RedirectResponse
    {
        abort_if($savingGoal->user_id !== $request->user()->id, 403);

        $this->savingGoalService->deleteGoal($savingGoal);

        return redirect()->route('saving-goals.index')->with('success', 'Target tabungan berhasil dihapus.');
    }
}