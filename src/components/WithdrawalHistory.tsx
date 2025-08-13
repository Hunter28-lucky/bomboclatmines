import { useState, useEffect } from 'react';
import { supabase } from '../supabaseClient';
import { format } from 'date-fns';

type WithdrawalHistory = {
  id: string;
  amount: string;
  upi_id: string;
  mobile_number: string;
  status: 'pending' | 'approved' | 'rejected';
  admin_note?: string | null;
  requested_at: string;
  processed_at: string | null;
  created_at: string;
};

export default function WithdrawalHistory() {
  const [withdrawals, setWithdrawals] = useState<WithdrawalHistory[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    fetchWithdrawals();
    // Set up real-time subscription
    const channel = supabase
      .channel('withdrawal-updates')
      .on('postgres_changes', 
        { event: '*', schema: 'public', table: 'withdrawals' },
        (payload) => {
          fetchWithdrawals(); // Refresh the list when there are changes
      })
      .subscribe();

    return () => {
      supabase.removeChannel(channel);
    };
  }, []);

  const fetchWithdrawals = async () => {
    try {
      const { data, error } = await supabase.rpc('get_withdrawal_history');
      
      if (error) throw error;
      
      setWithdrawals(data || []);
      setError(null);
    } catch (err) {
      console.error('Error fetching withdrawals:', err);
      setError(err instanceof Error ? err.message : 'Failed to fetch withdrawals');
    } finally {
      setLoading(false);
    }
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'pending':
        return 'bg-yellow-500/10 text-yellow-500';
      case 'approved':
        return 'bg-green-500/10 text-green-500';
      case 'rejected':
        return 'bg-red-500/10 text-red-500';
      default:
        return 'bg-gray-500/10 text-gray-500';
    }
  };

  if (loading) {
    return (
      <div className="flex justify-center items-center p-4">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-cyan-500"></div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="text-red-500 p-4 text-center bg-red-500/10 rounded-lg">
        {error}
      </div>
    );
  }

  if (withdrawals.length === 0) {
    return (
      <div className="text-gray-400 p-4 text-center bg-gray-800/50 rounded-lg">
        No withdrawal history found.
      </div>
    );
  }

  return (
    <div className="bg-gray-800/50 rounded-lg p-4 max-h-[60vh] overflow-y-auto">
      <h3 className="text-lg font-semibold mb-4 text-cyan-400">Withdrawal History</h3>
      <div className="space-y-3">
        {withdrawals.map((withdrawal) => (
          <div
            key={withdrawal.id}
            className="bg-gray-700/30 rounded-lg p-3 border border-gray-700/50 hover:border-cyan-500/30 transition-all duration-300"
          >
            <div className="flex justify-between items-start mb-2">
              <span className="font-bold text-green-400">₹{parseFloat(withdrawal.amount).toFixed(2)}</span>
              <span className={`px-2 py-1 rounded-full text-xs font-semibold ${getStatusColor(withdrawal.status)}`}>
                {withdrawal.status.charAt(0).toUpperCase() + withdrawal.status.slice(1)}
              </span>
            </div>
            <div className="text-sm text-gray-400 space-y-1">
              <p>UPI ID: {withdrawal.upi_id}</p>
              <p>Mobile: {withdrawal.mobile_number}</p>
              <p>Requested: {format(new Date(withdrawal.requested_at), 'MMM d, yyyy h:mm a')}</p>
              {withdrawal.processed_at && (
                <p>Processed: {format(new Date(withdrawal.processed_at), 'MMM d, yyyy h:mm a')}</p>
              )}
              {withdrawal.admin_note && (
                <p className="mt-2 p-2 bg-gray-800/50 rounded-lg border border-gray-700/50">
                  Note: {withdrawal.admin_note}
                </p>
              )}
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
