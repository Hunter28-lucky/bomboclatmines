import { useState } from 'react';
import { supabaseAdmin } from '../supabaseClient';

type Withdrawal = {
  id: string;
  user_id: string;
  email: string | null;
  full_name: string | null;
  amount: number;
  upi_id: string;
  status: 'pending' | 'approved' | 'rejected';
  created_at: string;
  processed_at: string | null;
  processed_by: string | null;
  notes: string | null;
};

export default function WithdrawalRow({ withdrawal, onUpdate }: { withdrawal: Withdrawal; onUpdate: () => void }) {
  const [loading, setLoading] = useState(false);
  const [note, setNote] = useState(withdrawal.notes || '');
  const [error, setError] = useState<string | null>(null);

  async function handleProcess(status: 'approved' | 'rejected') {
    setLoading(true);
    setError(null);
    
    try {
      const { error } = await supabaseAdmin.rpc('admin_process_withdrawal', {
        p_withdrawal_id: withdrawal.id,
        p_status: status,
        p_notes: note
      });

      if (error) throw error;
      onUpdate();
    } catch (err) {
      console.error('Error processing withdrawal:', err);
      setError(err instanceof Error ? err.message : 'Failed to process withdrawal');
    } finally {
      setLoading(false);
    }
  }

  return (
    <tr className="border-b border-gray-700">
      <td className="px-4 py-2">{withdrawal.user_id}</td>
      <td className="px-4 py-2">{withdrawal.email}</td>
      <td className="px-4 py-2">{withdrawal.full_name}</td>
      <td className="px-4 py-2 font-bold text-green-400">₹{withdrawal.amount}</td>
      <td className="px-4 py-2">N/A</td>
      <td className="px-4 py-2">{withdrawal.upi_id}</td>
      <td className="px-4 py-2">
        <span className={`px-2 py-1 rounded text-xs font-bold ${
          withdrawal.status === 'pending'
            ? 'bg-yellow-500/10 text-yellow-500'
            : withdrawal.status === 'approved'
            ? 'bg-green-500/10 text-green-500'
            : 'bg-red-500/10 text-red-500'
        }`}>
          {withdrawal.status}
        </span>
      </td>
      <td className="px-4 py-2">
        {withdrawal.status === 'pending' ? (
          <div className="space-y-2">
            <textarea
              value={note}
              onChange={(e) => setNote(e.target.value)}
              placeholder="Add a note (optional)"
              className="w-full px-2 py-1 rounded bg-gray-700 text-white border border-gray-600 focus:border-blue-400 focus:outline-none"
              rows={2}
            />
            <div className="flex gap-2">
              <button
                onClick={() => handleProcess('approved')}
                disabled={loading}
                className="px-3 py-1 bg-green-500 rounded text-white font-bold text-sm hover:bg-green-600"
              >
                Approve
              </button>
              <button
                onClick={() => handleProcess('rejected')}
                disabled={loading}
                className="px-3 py-1 bg-red-500 rounded text-white font-bold text-sm hover:bg-red-600"
              >
                Reject
              </button>
            </div>
            {error && <p className="text-red-500 text-sm">{error}</p>}
          </div>
        ) : (
          <>
            <p className="text-sm text-gray-400">{withdrawal.notes}</p>
            <p className="text-xs text-gray-500">
              {withdrawal.processed_at ? new Date(withdrawal.processed_at).toLocaleString() : ''}
            </p>
          </>
        )}
      </td>
    </tr>
  );
}
