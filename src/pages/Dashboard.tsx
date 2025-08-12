import { useState, useEffect } from 'react';
import { supabase } from '../supabaseClient';
import WithdrawalForm from '../components/WithdrawalForm';

type UserBalance = {
  user_id: string;
  email: string;
  full_name: string;
  balance: number;
  topups: number;
};

export default function Dashboard() {
  const [user, setUser] = useState<UserBalance | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [showWithdrawal, setShowWithdrawal] = useState(false);

  useEffect(() => {
    fetchUserData();
  }, []);

  async function fetchUserData() {
    try {
      const { data: userData, error: userError } = await supabase
        .rpc('get_user_details');

      if (userError) throw userError;
      
      if (userData && userData.length > 0) {
        setUser(userData[0]);
      }
    } catch (err) {
      console.error('Error fetching user data:', err);
      setError(err instanceof Error ? err.message : 'Failed to fetch user data');
    } finally {
      setLoading(false);
    }
  }

  if (loading) {
    return (
      <div className="flex justify-center items-center min-h-screen bg-gradient-to-br from-gray-900 to-blue-900">
        <div className="text-white text-xl">Loading...</div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="flex justify-center items-center min-h-screen bg-gradient-to-br from-gray-900 to-blue-900">
        <div className="bg-red-500/10 text-red-500 px-4 py-2 rounded-lg">{error}</div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-gray-900 to-blue-900 text-white p-4">
      <div className="max-w-4xl mx-auto space-y-8">
        {/* User Info Section */}
        <div className="bg-gray-800 rounded-lg p-6 shadow-lg">
          <h2 className="text-2xl font-bold mb-4">Account Overview</h2>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <p className="text-gray-400">Email</p>
              <p className="font-medium">{user?.email}</p>
            </div>
            <div>
              <p className="text-gray-400">Name</p>
              <p className="font-medium">{user?.full_name || 'Not set'}</p>
            </div>
            <div>
              <p className="text-gray-400">Balance</p>
              <p className="font-bold text-green-400">₹{user?.balance || 0}</p>
            </div>
            <div>
              <p className="text-gray-400">Total Topups</p>
              <p className="font-medium">{user?.topups || 0}</p>
            </div>
          </div>
        </div>

        {/* Withdrawal Section */}
        <div className="bg-gray-800 rounded-lg p-6 shadow-lg">
          <h2 className="text-2xl font-bold mb-4">Withdraw Funds</h2>
          <button
            className="mb-4 px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-400 rounded-xl text-white font-medium hover:from-green-600 hover:to-emerald-500 transition-all duration-300 shadow-xl hover:shadow-2xl"
            onClick={() => setShowWithdrawal(true)}
          >
            Request Withdrawal
          </button>
          {showWithdrawal && (
            <WithdrawalForm
              balance={user?.balance || 0}
              onClose={() => setShowWithdrawal(false)}
              onWithdrawalSubmitted={(amount) => {
                setUser(prev => prev ? { ...prev, balance: (prev.balance || 0) - amount } : prev);
                setShowWithdrawal(false);
              }}
            />
          )}
        </div>
      </div>
    </div>
  );
}
