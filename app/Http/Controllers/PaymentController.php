<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Support\DateFormat;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::with('student')
            ->when($request->month, function ($q, $month) {
                return $q->where('month', $month);
            })
            ->orderBy('month', 'desc')
            ->get()
            ->map(fn($payment) => $this->present($payment));

        return view('payment.index', compact('payments'));
    }

    public function show($id)
    {
        $payment = Payment::with('student')->findOrFail($id);
        $paymentData = $this->present($payment);

        return view('payment.show', compact('paymentData'));
    }

    public function edit($id)
    {
        $payment = Payment::with('student')->findOrFail($id);
        return view('payment.edit', compact('payment'));
    }

    public function update(Request $request, $id)
    {
        $payment = Payment::with('student')->findOrFail($id);

        $data = $request->validate([
            'month' => 'required|string|regex:/^\d{4}-\d{2}$/',
            'lesson_count' => 'required|integer|min:0',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'nullable|date',
            'status' => ['required', new Enum(PaymentStatus::class)],
        ]);

        if ((int) $data['lesson_count'] !== $payment->lesson_count
            && abs((float) $data['amount'] - (float) $payment->amount) < 0.01) {
            $price = $payment->student->price_per_lesson ?? 0;
            $data['amount'] = $data['lesson_count'] * $price;
        }

        $payment->update($data);

        return redirect()->route('payment.index');
    }

    public function markPaid($id)
    {
        $payment = Payment::findOrFail($id);

        $payment->update([
            'status' => PaymentStatus::Paid,
            'payment_date' => now()->format('Y-m-d'),
        ]);

        return redirect()->back();
    }

    private function present(Payment $payment): array
    {
        return [
            'id' => $payment->id,
            'student_id' => $payment->student_id,
            'student_name' => $payment->student->name,
            'student_price_per_lesson' => $payment->student->price_per_lesson ?? 0,
            'lesson_count' => $payment->lesson_count,
            'amount' => $payment->amount,
            'status' => $payment->status,
            'formatted_month' => ucfirst(Carbon::parse($payment->month)->translatedFormat('F Y')),
            'formatted_payment_date' => DateFormat::pl($payment->payment_date),
        ];
    }
}
