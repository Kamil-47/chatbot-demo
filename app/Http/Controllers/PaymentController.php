<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
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
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'student_name' => $payment->student->name,
                    'student_price_per_lesson' => $payment->student->price_per_lesson ?? 0,
                    'lesson_count' => $payment->lesson_count,
                    'amount' => $payment->amount,
                    'status' => $payment->status,
                    'formatted_month' => Carbon::parse($payment->month)->format('F Y'),
                    'formatted_payment_date' => $payment->payment_date
                        ? Carbon::parse($payment->payment_date)->format('d.m.Y')
                        : null,
                ];
            });

        return view('payment.index', compact('payments'));
    }

    public function show($id)
    {
        $payment = Payment::with('student')->findOrFail($id);

        $paymentData = [
            'id' => $payment->id,
            'student_name' => $payment->student->name,
            'student_price_per_lesson' => $payment->student->price_per_lesson ?? 0,
            'lesson_count' => $payment->lesson_count,
            'amount' => $payment->amount,
            'status' => $payment->status,
            'formatted_month' => Carbon::parse($payment->month)->format('F Y'),
            'formatted_payment_date' => $payment->payment_date
                ? Carbon::parse($payment->payment_date)->format('d.m.Y')
                : null,
        ];

        return view('payment.show', compact('paymentData'));
    }

    public function edit($id)
    {
        $payment = Payment::with('student')->findOrFail($id);
        return view('payment.edit', compact('payment'));
    }

    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $payment->update([
            'month' => $request->month,
            'lesson_count' => $request->lesson_count,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'status' => $request->status,
        ]);

        return redirect()->route('payment.index');
    }

    public function markPaid($id)
    {
        $payment = Payment::findOrFail($id);

        $payment->update([
            'status' => 'paid',
            'payment_date' => now()->format('Y-m-d'),
        ]);

        return redirect()->back();
    }

    public function create()
    {
        return view('payment.create');
    }

    public function store(Request $request)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}