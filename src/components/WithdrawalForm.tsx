import { useState } from 'react';
import { supabase } from '../supabaseClient';

export default function WithdrawalForm() {
  const [amount, setAmount] = useState<string>('');
  const [mobileNumber, setMobileNumber] = useState<string>('');
  const [upiId, setUpiId] = useState<string>('');
  const [loading, setLoading] = useState(false);
  const [message, setMessage] = useState<{ text: string; type: 'success' | 'error' } | null>(null);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    setMessage(null);

    try {
      const numericAmount = parseFloat(amount);
      if (isNaN(numericAmount) || numericAmount <= 0) {
        setMessage({ text: 'Please enter a valid amount', type: 'error' });
        return;
      }

      const { data, error } = await supabase
        .rpc('request_withdrawal', {
          p_amount: numericAmount,
          p_mobile_number: mobileNumber,
          p_upi_id: upiId
        });

      if (error) throw error;

      if (data.success) {
        setMessage({ text: data.message + '. Please allow 0-7 working days for processing.', type: 'success' });
        // Clear form
        setAmount('');
        setMobileNumber('');
        setUpiId('');
      } else {
        setMessage({ text: data.message, type: 'error' });
      }
    } catch (err) {
      console.error('Error:', err);
      setMessage({ 
        text: err instanceof Error ? err.message : 'Failed to submit withdrawal request', 
        type: 'error' 
      });
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="max-w-md mx-auto p-6 bg-gray-800 rounded-lg shadow-lg">
      <h2 className="text-2xl font-bold mb-6 text-white">Withdraw Funds</h2>
      
      {message && (
        <div className={`p-4 rounded-lg mb-4 ${
          message.type === 'success' ? 'bg-green-500/10 text-green-500' : 'bg-red-500/10 text-red-500'
        }`}>
          {message.text}
        </div>
      )}

      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label htmlFor="amount" className="block text-sm font-medium text-gray-300">
            Amount (₹)
          </label>
          <input
            id="amount"
            type="number"
            value={amount}
            onChange={(e) => setAmount(e.target.value)}
            min="0"
            step="1"
            required
            className="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          />
        </div>

        <div>
          <label htmlFor="mobile" className="block text-sm font-medium text-gray-300">
            Mobile Number
          </label>
          <input
            id="mobile"
            type="tel"
            value={mobileNumber}
            onChange={(e) => setMobileNumber(e.target.value)}
            pattern="[0-9]{10}"
            required
            className="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          />
        </div>

        <div>
          <label htmlFor="upi" className="block text-sm font-medium text-gray-300">
            UPI ID
          </label>
          <input
            id="upi"
            type="text"
            value={upiId}
            onChange={(e) => setUpiId(e.target.value)}
            required
            className="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          />
        </div>

        <button
          type="submit"
          disabled={loading}
          className={`w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white ${
            loading
              ? 'bg-indigo-500/50 cursor-not-allowed'
              : 'bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500'
          }`}
        >
          {loading ? 'Processing...' : 'Submit Withdrawal Request'}
        </button>
      </form>
    </div>
  );
}
