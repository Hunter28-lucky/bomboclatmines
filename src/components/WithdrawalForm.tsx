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
    <div className="fixed inset-0 bg-black/60 backdrop-blur-md flex items-center justify-center z-50 p-4">
      <div className="relative w-full max-w-md mx-auto card overflow-hidden animate-scale-in">
        <div className="flex items-center justify-between p-4 border-b border-white/20">
          <h2 className="text-xl font-bold text-gradient-success">
            Withdraw Funds
          </h2>
          <button
            onClick={onClose}
            className="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-700/50 hover:bg-slate-600/50 transition-all duration-300 hover:shadow-lg"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        {/* Tabs */}
        <div className="flex border-b border-white/20">
          <button
            className={`flex-1 px-4 py-3 text-sm font-medium transition-all duration-300 ${
              activeTab === 'new'
                ? 'bg-blue-500/10 text-blue-400 border-b-2 border-blue-500'
                : 'text-gray-400 hover:text-gray-300 hover:bg-white/5'
            }`}
            onClick={() => setActiveTab('new')}
          >
            New Withdrawal
          </button>
          <button
            className={`flex-1 px-4 py-3 text-sm font-medium transition-all duration-300 ${
              activeTab === 'history'
                ? 'bg-blue-500/10 text-blue-400 border-b-2 border-blue-500'
                : 'text-gray-400 hover:text-gray-300 hover:bg-white/5'
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
                <label className="block text-sm font-medium text-gray-300 mb-2">Amount (min. ₹500)</label>
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
                  className="input-field"
                />
                {/* Quick amount buttons */}
                <div className="grid grid-cols-4 gap-2 mt-3">
                  {[500, 1000, 2000, 5000].map((preset) => (
                    <button
                      key={preset}
                      type="button"
                      onClick={() => preset <= balance && setAmount(preset)}
                      disabled={preset > balance}
                      className={`px-3 py-2 rounded-lg text-xs font-medium transition-all duration-300 ${
                        amount === preset
                          ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/25'
                          : preset <= balance
                          ? 'bg-slate-700/50 hover:bg-slate-600/50 text-gray-300 border border-slate-600/50 hover:border-slate-500/50'
                          : 'bg-slate-800/50 text-gray-500 cursor-not-allowed border border-slate-700/50'
                      }`}
                    >
                      ₹{preset}
                    </button>
                  ))}
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-300 mb-2">Mobile Number</label>
                <input
                  type="tel"
                  value={mobileNumber}
                  onChange={(e) => setMobileNumber(e.target.value.replace(/\D/g, '').slice(0, 10))}
                  placeholder="10-digit mobile number"
                  pattern="[0-9]{10}"
                  maxLength={10}
                  className="input-field"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-300 mb-2">UPI ID</label>
                <input
                  type="text"
                  value={upiId}
                  onChange={(e) => setUpiId(e.target.value)}
                  placeholder="example@upi"
                  className="input-field"
                />
              </div>
              {error && (
                <div className="p-3 bg-red-500/10 border border-red-500/30 rounded-lg text-red-400 text-sm animate-fade-in">
                  {error}
                </div>
              )}
              <button
                type="submit"
                disabled={loading}
                className="btn-success w-full py-3 font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {loading ? (
                  <div className="flex items-center justify-center gap-2">
                    <div className="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                    Processing...
                  </div>
                ) : (
                  'Submit Withdrawal'
                )}
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
