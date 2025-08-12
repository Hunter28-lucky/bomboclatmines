import { useState } from 'react';
import { supabase } from './supabaseClient';
import { X, Loader2 } from 'lucide-react';

interface WithdrawalFormProps {
  onClose: () => void;
  balance: number;
  onWithdrawalSubmitted: (amount: number) => void;
}

export default function WithdrawalForm({ onClose, balance, onWithdrawalSubmitted }: WithdrawalFormProps) {
  const [amount, setAmount] = useState('');
  const [upiId, setUpiId] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [error, setError] = useState('');

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setIsSubmitting(true);

    const withdrawalAmount = Number(amount);

    if (withdrawalAmount < 500) {
      setError('Minimum withdrawal amount is ₹500');
      setIsSubmitting(false);
      return;
    }

    if (withdrawalAmount > balance) {
      setError('Withdrawal amount cannot exceed your balance');
      setIsSubmitting(false);
      return;
    }

    if (!upiId.includes('@')) {
      setError('Please enter a valid UPI ID');
      setIsSubmitting(false);
      return;
    }

    try {
      const { error: withdrawalError } = await supabase
        .rpc('request_withdrawal', {
          p_amount: withdrawalAmount,
          p_upi_id: upiId
        });

      if (withdrawalError) {
        throw withdrawalError;
      }

      onWithdrawalSubmitted(withdrawalAmount);
      onClose();
    } catch (error: any) {
      setError(error.message || 'Failed to submit withdrawal request');
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div className="bg-white rounded-3xl shadow-2xl w-full max-w-md animate-fade-in">
        {/* Header */}
        <div className="flex items-center justify-between p-6 border-b border-gray-100">
          <h2 className="text-xl font-bold text-gray-900">Withdraw Funds</h2>
          <button
            onClick={onClose}
            className="text-gray-400 hover:text-gray-500 transition-colors"
          >
            <X className="h-6 w-6" />
          </button>
        </div>

        {/* Form */}
        <form onSubmit={handleSubmit} className="p-6 space-y-6">
          <div className="space-y-2">
            <label htmlFor="amount" className="block text-sm font-medium text-gray-700">
              Amount to Withdraw
            </label>
            <div className="relative">
              <span className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">₹</span>
              <input
                id="amount"
                type="number"
                min="500"
                max={balance}
                value={amount}
                onChange={(e) => setAmount(e.target.value)}
                className="pl-8 pr-4 py-3 w-full rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-400 transition-all"
                placeholder="Enter amount"
                required
              />
            </div>
            <p className="text-xs text-gray-500">Minimum withdrawal: ₹500</p>
          </div>

          <div className="space-y-2">
            <label htmlFor="upi" className="block text-sm font-medium text-gray-700">
              UPI ID
            </label>
            <input
              id="upi"
              type="text"
              value={upiId}
              onChange={(e) => setUpiId(e.target.value)}
              className="px-4 py-3 w-full rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-400 transition-all"
              placeholder="your.id@upi"
              required
            />
            <p className="text-xs text-gray-500">Enter your UPI ID to receive the payment</p>
          </div>

          {error && (
            <div className="p-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
              {error}
            </div>
          )}

          <button
            type="submit"
            disabled={isSubmitting}
            className="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white font-semibold shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {isSubmitting ? (
              <span className="flex items-center justify-center gap-2">
                <Loader2 className="h-5 w-5 animate-spin" />
                Processing...
              </span>
            ) : (
              'Submit Withdrawal Request'
            )}
          </button>
        </form>

        <div className="px-6 py-4 bg-gray-50 text-xs text-gray-500 rounded-b-3xl text-center">
          Withdrawal requests are typically processed within 24 hours
        </div>
      </div>
    </div>
  );
}
