import { useState } from 'react';
import { supabaseAdmin } from '../supabaseClient';
import { CheckCircle, XCircle, Clock, MessageSquare, User, CreditCard, Smartphone } from 'lucide-react';

type Withdrawal = {
  id: string;
  user_id: string;
  email: string;
  full_name: string | null;
  amount: string;
  mobile_number: string;
  upi_id: string;
  status: 'pending' | 'approved' | 'rejected';
  admin_note: string | null;
  requested_at: string;
  processed_at: string | null;
  created_at: string;
};

export default function WithdrawalRow({ withdrawal, onUpdate }: { withdrawal: Withdrawal; onUpdate: () => void }) {
  const [loading, setLoading] = useState(false);
  const [note, setNote] = useState(withdrawal.admin_note || '');
  const [error, setError] = useState<string | null>(null);

  async function handleProcess(status: 'approved' | 'rejected') {
    setLoading(true);
    setError(null);
    
    try {
      const { error } = await supabaseAdmin.rpc('admin_process_withdrawal', {
        p_withdrawal_id: withdrawal.id,
        p_status: status,
        p_admin_note: note
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

  const getStatusIcon = (status: string) => {
    switch (status) {
      case 'pending':
        return <Clock className="w-4 h-4" />;
      case 'approved':
        return <CheckCircle className="w-4 h-4" />;
      case 'rejected':
        return <XCircle className="w-4 h-4" />;
      default:
        return <Clock className="w-4 h-4" />;
    }
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'pending':
        return 'bg-yellow-500/10 text-yellow-500 border-yellow-500/30';
      case 'approved':
        return 'bg-green-500/10 text-green-500 border-green-500/30';
      case 'rejected':
        return 'bg-red-500/10 text-red-500 border-red-500/30';
      default:
        return 'bg-gray-500/10 text-gray-500 border-gray-500/30';
    }
  };

  return (
    <tr className="hover:bg-white/5 transition-colors duration-200">
      <td className="px-4 py-3">
        <div className="flex items-center space-x-2">
          <div className="w-8 h-8 bg-blue-500/20 rounded-full flex items-center justify-center">
            <User className="w-4 h-4 text-blue-400" />
          </div>
          <span className="text-sm text-white font-mono">{withdrawal.user_id.slice(0, 8)}...</span>
        </div>
      </td>
      <td className="px-4 py-3">
        <p className="text-sm text-gray-300 truncate max-w-32">{withdrawal.email}</p>
      </td>
      <td className="px-4 py-3">
        <p className="text-sm text-white font-medium">{withdrawal.full_name || 'Unknown'}</p>
      </td>
      <td className="px-4 py-3">
        <div className="flex items-center space-x-2">
          <CreditCard className="w-4 h-4 text-green-400" />
          <span className="text-lg font-bold text-gradient-success">₹{parseFloat(withdrawal.amount).toFixed(2)}</span>
        </div>
      </td>
      <td className="px-4 py-3">
        <div className="flex items-center space-x-2">
          <Smartphone className="w-4 h-4 text-blue-400" />
          <span className="text-sm text-gray-300">{withdrawal.mobile_number}</span>
        </div>
      </td>
      <td className="px-4 py-3">
        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-500/10 text-purple-400 font-mono">
          {withdrawal.upi_id}
        </span>
      </td>
      <td className="px-4 py-3">
        <span className={`inline-flex items-center space-x-1 px-3 py-1.5 rounded-full text-xs font-medium border ${getStatusColor(withdrawal.status)}`}>
          {getStatusIcon(withdrawal.status)}
          <span className="capitalize">{withdrawal.status}</span>
        </span>
      </td>
      <td className="px-4 py-3">
        {withdrawal.status === 'pending' ? (
          <div className="space-y-3">
            <div className="flex items-start space-x-2">
              <MessageSquare className="w-4 h-4 text-blue-400 mt-1 flex-shrink-0" />
              <textarea
                value={note}
                onChange={(e) => setNote(e.target.value)}
                placeholder="Add a note (optional)"
                className="flex-1 px-3 py-2 rounded-lg bg-slate-700/50 text-white border border-slate-600/50 focus:border-blue-500/50 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all duration-200 resize-none"
                rows={2}
              />
            </div>
            <div className="flex items-center space-x-2">
              <button
                onClick={() => handleProcess('approved')}
                disabled={loading}
                className="inline-flex items-center px-3 py-2 bg-green-500 hover:bg-green-600 rounded-lg text-white text-sm font-medium transition-all duration-200 hover:shadow-lg hover:shadow-green-500/25 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {loading ? (
                  <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                ) : (
                  <>
                    <CheckCircle className="w-4 h-4 mr-1" />
                    Approve
                  </>
                )}
              </button>
              <button
                onClick={() => handleProcess('rejected')}
                disabled={loading}
                className="inline-flex items-center px-3 py-2 bg-red-500 hover:bg-red-600 rounded-lg text-white text-sm font-medium transition-all duration-200 hover:shadow-lg hover:shadow-red-500/25 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {loading ? (
                  <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                ) : (
                  <>
                    <XCircle className="w-4 h-4 mr-1" />
                    Reject
                  </>
                )}
              </button>
            </div>
            {error && (
              <div className="p-2 bg-red-500/10 border border-red-500/30 rounded-lg">
                <p className="text-red-400 text-xs">{error}</p>
              </div>
            )}
          </div>
        ) : (
          <div className="space-y-2">
            {withdrawal.admin_note && (
              <div className="flex items-start space-x-2">
                <MessageSquare className="w-4 h-4 text-blue-400 mt-1 flex-shrink-0" />
                <p className="text-sm text-gray-300 bg-slate-800/50 p-2 rounded-lg flex-1">
                  {withdrawal.admin_note}
                </p>
              </div>
            )}
            {withdrawal.processed_at && (
              <div className="flex items-center space-x-2 text-xs text-gray-500">
                <Clock className="w-3 h-3" />
                <span>Processed: {new Date(withdrawal.processed_at).toLocaleString()}</span>
              </div>
            )}
          </div>
        )}
      </td>
    </tr>
  );
}
