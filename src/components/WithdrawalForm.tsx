import { useState } from 'react';
import { supabase } from '../supabaseClient';
import WithdrawalHistory from './WithdrawalHistory';
import { X } from 'lucide-react';

type Props = {
  onClose: () => void;
  balance: number;
  onWithdrawalSubmitted: (amount: number) => void;
};

export default function WithdrawalForm({ onClose, balance, onWithdrawalSubmitted }: Props) {
  const [amount, setAmount] = useState(500);
  const [upiId, setUpiId] = useState('');
  const [mobileNumber, setMobileNumber] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [activeTab, setActiveTab] = useState<'new' | 'history'>('new');

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError(null);

    if (amount < 500) {
      setError('Minimum withdrawal amount is ₹500');
      setLoading(false);
      return;
    }

    if (amount > balance) {
      setError('Insufficient balance');
      setLoading(false);
      return;
    }

    if (!upiId) {
      setError('Please enter your UPI ID');
      setLoading(false);
      return;
    }

    if (!mobileNumber || !/^\d{10}$/.test(mobileNumber)) {
      setError('Please enter a valid 10-digit mobile number');
      setLoading(false);
      return;
    }

    try {
      console.log('Submitting withdrawal with:', {
        p_amount: amount,
        p_upi_id: upiId,
        p_mobile_number: mobileNumber,
      });
      
      const { data, error: withdrawalError } = await supabase.rpc('request_withdrawal', {
        p_amount: amount,
        p_upi_id: upiId,
        p_mobile_number: mobileNumber,
      });

      if (withdrawalError) throw withdrawalError;

      if (data.success === false) {
        throw new Error(data.error || 'Failed to process withdrawal');
      }

      onWithdrawalSubmitted(amount);
      setActiveTab('history'); // Switch to history tab after successful submission
    } catch (err) {
      console.error('Error processing withdrawal:', err);
      setError(err instanceof Error ? err.message : 'Failed to process withdrawal');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div className="relative w-full max-w-md mx-auto bg-gradient-to-br from-gray-800 to-slate-800 rounded-xl border border-gray-700/50 overflow-hidden">
        <div className="flex items-center justify-between p-4 border-b border-gray-700/50">
          <h2 className="text-xl font-bold bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent">
            Withdraw Funds
          </h2>
          <button
            onClick={onClose}
            className="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-700/50 hover:bg-gray-600/50 transition-all"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        {/* Tabs */}
        <div className="flex border-b border-gray-700/50">
          <button
            className={`flex-1 px-4 py-2 text-sm font-medium transition-all duration-300 ${
              activeTab === 'new'
                ? 'bg-cyan-500/10 text-cyan-400 border-b-2 border-cyan-500'
                : 'text-gray-400 hover:text-gray-300'
            }`}
            onClick={() => setActiveTab('new')}
          >
            New Withdrawal
          </button>
          <button
            className={`flex-1 px-4 py-2 text-sm font-medium transition-all duration-300 ${
              activeTab === 'history'
                ? 'bg-cyan-500/10 text-cyan-400 border-b-2 border-cyan-500'
                : 'text-gray-400 hover:text-gray-300'
            }`}
            onClick={() => setActiveTab('history')}
          >
            History
          </button>
        </div>

        <div className="p-4">
          {activeTab === 'new' ? (
            <form onSubmit={handleSubmit} className="space-y-4">
              <div>
                <label className="block text-sm text-gray-400 mb-1">Amount (min. ₹500)</label>
                <input
                  type="number"
                  value={amount}
                  onChange={(e) => {
                    const value = Number(e.target.value);
                    if (value >= 500 && value <= balance) {
                      setAmount(value);
                    }
                  }}
                  min={500}
                  max={balance}
                  className="w-full px-3 py-2 bg-gray-700/50 rounded-lg border border-gray-600 focus:border-cyan-400 focus:outline-none"
                />
                {/* Quick amount buttons */}
                <div className="grid grid-cols-4 gap-2 mt-2">
                  {[500, 1000, 2000, 5000].map((preset) => (
                    <button
                      key={preset}
                      type="button"
                      onClick={() => preset <= balance && setAmount(preset)}
                      disabled={preset > balance}
                      className={`px-2 py-1 rounded text-xs font-medium transition-all duration-300 ${
                        amount === preset
                          ? 'bg-cyan-500 text-white'
                          : preset <= balance
                          ? 'bg-gray-700 hover:bg-gray-600 text-gray-300'
                          : 'bg-gray-800 text-gray-500 cursor-not-allowed'
                      }`}
                    >
                      ₹{preset}
                    </button>
                  ))}
                </div>
              </div>
              <div>
                <label className="block text-sm text-gray-400 mb-1">Mobile Number</label>
                <input
                  type="tel"
                  value={mobileNumber}
                  onChange={(e) => setMobileNumber(e.target.value.replace(/\D/g, '').slice(0, 10))}
                  placeholder="10-digit mobile number"
                  pattern="[0-9]{10}"
                  maxLength={10}
                  className="w-full px-3 py-2 bg-gray-700/50 rounded-lg border border-gray-600 focus:border-cyan-400 focus:outline-none"
                />
              </div>
              <div>
                <label className="block text-sm text-gray-400 mb-1">UPI ID</label>
                <input
                  type="text"
                  value={upiId}
                  onChange={(e) => setUpiId(e.target.value)}
                  placeholder="example@upi"
                  className="w-full px-3 py-2 bg-gray-700/50 rounded-lg border border-gray-600 focus:border-cyan-400 focus:outline-none"
                />
              </div>
              {error && (
                <div className="p-2 bg-red-500/10 border border-red-500/20 rounded text-red-400 text-sm">
                  {error}
                </div>
              )}
              <button
                type="submit"
                disabled={loading}
                className="w-full py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 rounded-lg font-semibold disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300"
              >
                {loading ? 'Processing...' : 'Submit Withdrawal'}
              </button>
            </form>
          ) : (
            <WithdrawalHistory />
          )}
        </div>
      </div>
    </div>
  );
}
