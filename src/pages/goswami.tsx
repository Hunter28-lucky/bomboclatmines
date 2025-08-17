import { useState, useEffect } from 'react';
import UserRow from '../components/UserRow';
import WithdrawalRow from '../components/WithdrawalRow';
import { supabase, supabaseAdmin } from '../supabaseClient';
import { Users, CreditCard, TrendingUp, Shield, Activity, Eye, BarChart3 } from 'lucide-react';

const ADMIN_EMAIL = 'krrishyogi18@gmail.com';

type UserBalance = {
  user_id: string;
  email: string | null;
  full_name: string | null;
  balance: number;
  topups: number | null;
};

type Payment = {
  id: number;
  site_id: string;
  mobile_number: string;
  utr_number: string;
  screenshot_url?: string;
  status: string;
  created_at: string;
};

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

export default function AdminPage() {
  const [users, setUsers] = useState<UserBalance[]>([]);
  const [payments, setPayments] = useState<Payment[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);
  const [withdrawals, setWithdrawals] = useState<Withdrawal[]>([]);
  const [activeTab, setActiveTab] = useState<'overview' | 'users' | 'payments' | 'withdrawals'>('overview');

  useEffect(() => {
    checkAdminAndFetchData();
  }, []);

  async function checkAdminAndFetchData() {
    try {
      // First check session
      const { data: { session }, error: sessionError } = await supabase.auth.getSession();
      if (sessionError) {
        throw new Error(`Session error: ${sessionError.message}`);
      }
      
      if (!session?.user) {
        throw new Error('No active session');
      }

      // Then verify user details
      const { data: { user }, error: userError } = await supabase.auth.getUser();
      if (userError) {
        throw new Error(`User verification error: ${userError.message}`);
      }
      
      if (!user || user.email !== ADMIN_EMAIL) {
        throw new Error('Unauthorized access: Not an admin user');
      }

      // After successful authentication, fetch all data
      await fetchAllData();
    } catch (err) {
      console.error('Admin panel error:', err);
      const errorMessage = err instanceof Error ? err.message : 'An error occurred';
      setError(errorMessage);
      if (errorMessage.includes('Unauthorized')) {
        window.location.replace('/');
      }
    } finally {
      setLoading(false);
    }
  }

  async function fetchData() {
    setLoading(true);
    setError(null);
    try {
      // Fetch all user details using secure RPC function
      const { data: userData, error: userError } = await supabaseAdmin
        .rpc('get_all_user_details');

      if (userError) {
        console.error('User details error:', userError);
        setError('Failed to fetch user details: ' + userError.message);
        return;
      }

      // Fetch additional user details from auth.users
      const { data: authUsers, error: authError } = await supabaseAdmin.auth.admin.listUsers();
      if (authError) {
        console.error('Auth users error:', authError);
        setError('Failed to fetch auth users: ' + authError.message);
        return;
      }

      // Merge user data
      const mergedUsers = userData.map((user: any) => {
        const authUser = authUsers.users.find((au: any) => au.id === user.user_id);
        return {
          ...user,
          email: authUser?.email || user.email,
          full_name: authUser?.user_metadata?.full_name || authUser?.user_metadata?.name || user.full_name
        };
      });

      setUsers(mergedUsers);

      // Fetch payments
      const { data: paymentData, error: paymentError } = await supabaseAdmin
        .from('payments')
        .select('*')
        .order('created_at', { ascending: false });

      if (paymentError) {
        console.error('Payments error:', paymentError);
        setError('Failed to fetch payments: ' + paymentError.message);
        return;
      }

      setPayments(paymentData || []);
    } catch (err) {
      console.error('Error fetching data:', err);
      setError(err instanceof Error ? err.message : 'Failed to fetch data');
      setUsers([]);
      setPayments([]);
    }
  }

  async function fetchWithdrawals() {
    try {
      const { data, error } = await supabaseAdmin
        .from('withdrawals')
        .select('*')
        .order('created_at', { ascending: false });

      if (error) throw error;

      // Fetch user details for each withdrawal
      const withdrawalsWithUserDetails = await Promise.all(
        (data || []).map(async (withdrawal) => {
          const { data: userData } = await supabaseAdmin.auth.admin.getUserById(withdrawal.user_id);
          return {
            ...withdrawal,
            email: userData?.user?.email || 'Unknown',
            full_name: userData?.user?.user_metadata?.full_name || userData?.user?.user_metadata?.name || 'Unknown'
          };
        })
      );

      setWithdrawals(withdrawalsWithUserDetails);
    } catch (err) {
      console.error('Error fetching withdrawals:', err);
      setError(err instanceof Error ? err.message : 'Failed to fetch withdrawals');
      setWithdrawals([]);
    }
  }

  async function fetchAllData() {
    try {
      await Promise.all([
        fetchData(),
        fetchWithdrawals()
      ]);
    } catch (err) {
      console.error('Error fetching all data:', err);
      setError(err instanceof Error ? err.message : 'Failed to fetch data');
    } finally {
      setLoading(false);
    }
  }

  // Calculate statistics
  const totalUsers = users.length;
  const totalBalance = users.reduce((sum, user) => sum + user.balance, 0);
  const totalTopups = users.reduce((sum, user) => sum + (user.topups || 0), 0);
  const pendingWithdrawals = withdrawals.filter(w => w.status === 'pending').length;
  const totalPayments = payments.length;

  if (loading) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 flex items-center justify-center">
        <div className="text-center">
          <div className="relative">
            <div className="w-16 h-16 border-4 border-blue-500/30 border-t-blue-500 rounded-full animate-spin"></div>
            <div className="absolute inset-0 w-16 h-16 border-4 border-transparent border-t-purple-500 rounded-full animate-spin" style={{ animationDirection: 'reverse', animationDuration: '1.5s' }}></div>
          </div>
          <p className="text-blue-400 text-lg font-semibold mt-4 animate-pulse">Loading Admin Panel...</p>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 flex items-center justify-center p-4">
        <div className="card p-8 text-center max-w-md">
          <div className="w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <Shield className="w-8 h-8 text-red-500" />
          </div>
          <h2 className="text-xl font-bold text-red-400 mb-2">Access Error</h2>
          <p className="text-gray-300 mb-4">{error}</p>
          <button
            onClick={() => window.location.replace('/')}
            className="btn-primary"
          >
            Return to Home
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 text-white">
      {/* Header */}
      <div className="bg-gradient-to-r from-slate-800/90 to-blue-900/90 backdrop-blur-md border-b border-white/20 shadow-xl sticky top-0 z-40">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-between h-16">
            <div className="flex items-center space-x-3">
              <div className="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                <Shield className="w-6 h-6 text-white" />
              </div>
              <h1 className="text-xl font-bold text-gradient">Admin Dashboard</h1>
            </div>
            <div className="flex items-center space-x-2">
              <span className="text-sm text-gray-400">Welcome, Admin</span>
              <div className="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                <span className="text-white text-sm font-bold">A</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Navigation Tabs */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div className="flex space-x-1 bg-slate-800/50 backdrop-blur-sm rounded-xl p-1 border border-white/20">
          {[
            { id: 'overview', label: 'Overview', icon: BarChart3 },
            { id: 'users', label: 'Users', icon: Users },
            { id: 'payments', label: 'Payments', icon: CreditCard },
            { id: 'withdrawals', label: 'Withdrawals', icon: TrendingUp }
          ].map((tab) => (
            <button
              key={tab.id}
              onClick={() => setActiveTab(tab.id as any)}
              className={`flex items-center space-x-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 ${
                activeTab === tab.id
                  ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/25'
                  : 'text-gray-400 hover:text-white hover:bg-white/5'
              }`}
            >
              <tab.icon className="w-4 h-4" />
              <span>{tab.label}</span>
            </button>
          ))}
        </div>
      </div>

      {/* Content */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        {activeTab === 'overview' && (
          <div className="space-y-6">
            {/* Statistics Cards */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              <div className="card-hover p-6">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm font-medium text-gray-400">Total Users</p>
                    <p className="text-2xl font-bold text-white">{totalUsers}</p>
                  </div>
                  <div className="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                    <Users className="w-6 h-6 text-blue-400" />
                  </div>
                </div>
              </div>

              <div className="card-hover p-6">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm font-medium text-gray-400">Total Balance</p>
                    <p className="text-2xl font-bold text-green-400">₹{totalBalance.toLocaleString()}</p>
                  </div>
                  <div className="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                    <CreditCard className="w-6 h-6 text-green-400" />
                  </div>
                </div>
              </div>

              <div className="card-hover p-6">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm font-medium text-gray-400">Total Topups</p>
                    <p className="text-2xl font-bold text-purple-400">₹{totalTopups.toLocaleString()}</p>
                  </div>
                  <div className="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                    <TrendingUp className="w-6 h-6 text-purple-400" />
                  </div>
                </div>
              </div>

              <div className="card-hover p-6">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm font-medium text-gray-400">Pending Withdrawals</p>
                    <p className="text-2xl font-bold text-yellow-400">{pendingWithdrawals}</p>
                  </div>
                  <div className="w-12 h-12 bg-yellow-500/20 rounded-xl flex items-center justify-center">
                    <Activity className="w-6 h-6 text-yellow-400" />
                  </div>
                </div>
              </div>
            </div>

            {/* Recent Activity */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <div className="card-hover p-6">
                <h3 className="text-lg font-semibold text-white mb-4 flex items-center space-x-2">
                  <Eye className="w-5 h-5 text-blue-400" />
                  <span>Recent Users</span>
                </h3>
                <div className="space-y-3">
                  {users.slice(0, 5).map((user) => (
                    <div key={user.user_id} className="flex items-center space-x-3 p-3 bg-slate-800/50 rounded-lg">
                      <img 
                        src={`https://ui-avatars.com/api/?name=${user.full_name || user.email}&background=0ea5e9&color=fff`} 
                        alt="avatar" 
                        className="w-8 h-8 rounded-full" 
                      />
                      <div className="flex-1 min-w-0">
                        <p className="text-sm font-medium text-white truncate">{user.full_name || 'Unknown'}</p>
                        <p className="text-xs text-gray-400 truncate">{user.email}</p>
                      </div>
                      <div className="text-right">
                        <p className="text-sm font-bold text-green-400">₹{user.balance}</p>
                      </div>
                    </div>
                  ))}
                </div>
              </div>

              <div className="card-hover p-6">
                <h3 className="text-lg font-semibold text-white mb-4 flex items-center space-x-2">
                  <Activity className="w-5 h-5 text-green-400" />
                  <span>Recent Withdrawals</span>
                </h3>
                <div className="space-y-3">
                  {withdrawals.slice(0, 5).map((withdrawal) => (
                    <div key={withdrawal.id} className="flex items-center justify-between p-3 bg-slate-800/50 rounded-lg">
                      <div className="flex-1 min-w-0">
                        <p className="text-sm font-medium text-white truncate">{withdrawal.full_name || 'Unknown'}</p>
                        <p className="text-xs text-gray-400 truncate">{withdrawal.email}</p>
                      </div>
                      <div className="text-right">
                        <p className="text-sm font-bold text-green-400">₹{parseFloat(withdrawal.amount).toFixed(2)}</p>
                        <span className={`inline-block px-2 py-1 rounded-full text-xs font-medium ${
                          withdrawal.status === 'pending'
                            ? 'bg-yellow-500/10 text-yellow-500'
                            : withdrawal.status === 'approved'
                            ? 'bg-green-500/10 text-green-500'
                            : 'bg-red-500/10 text-red-500'
                        }`}>
                          {withdrawal.status}
                        </span>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </div>
        )}

        {activeTab === 'users' && (
          <div className="card-hover p-6">
            <div className="flex items-center justify-between mb-6">
              <h2 className="text-2xl font-bold text-white flex items-center space-x-2">
                <Users className="w-6 h-6 text-blue-400" />
                <span>User Management</span>
              </h2>
              <div className="text-sm text-gray-400">
                Total: {users.length} users
              </div>
            </div>
            <div className="overflow-x-auto">
              <table className="min-w-full">
                <thead>
                  <tr className="border-b border-white/20">
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">Profile</th>
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">Email</th>
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">Name</th>
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">Balance</th>
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">Topups</th>
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">Actions</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-white/10">
                  {users.map((user: UserBalance) => (
                    <UserRow 
                      key={user.user_id} 
                      user={user} 
                      onUpdate={fetchData} 
                    />
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}

        {activeTab === 'payments' && (
          <div className="card-hover p-6">
            <div className="flex items-center justify-between mb-6">
              <h2 className="text-2xl font-bold text-white flex items-center space-x-2">
                <CreditCard className="w-6 h-6 text-green-400" />
                <span>Payment Records</span>
              </h2>
              <div className="text-sm text-gray-400">
                Total: {payments.length} payments
              </div>
            </div>
            <div className="overflow-x-auto">
              <table className="min-w-full">
                <thead>
                  <tr className="border-b border-white/20">
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">User</th>
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">Email</th>
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">Name</th>
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">Mobile</th>
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">UTR</th>
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">Screenshot</th>
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">Status</th>
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">Submitted At</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-white/10">
                  {payments.map((payment: Payment) => {
                    const userMeta = users.find((u: UserBalance) => u.user_id === payment.site_id);
                    return (
                      <tr key={payment.id} className="hover:bg-white/5 transition-colors duration-200">
                        <td className="px-4 py-3 text-sm text-white">{payment.site_id}</td>
                        <td className="px-4 py-3 text-sm text-gray-300">{userMeta?.email || ''}</td>
                        <td className="px-4 py-3 text-sm text-gray-300">{userMeta?.full_name || ''}</td>
                        <td className="px-4 py-3 text-sm text-gray-300">{payment.mobile_number}</td>
                        <td className="px-4 py-3 text-sm text-gray-300 font-mono">{payment.utr_number}</td>
                        <td className="px-4 py-3">
                          {payment.screenshot_url ? (
                            <a href={payment.screenshot_url} target="_blank" rel="noopener noreferrer" className="inline-block">
                              <img src={payment.screenshot_url} alt="screenshot" className="w-16 h-16 rounded-lg border border-blue-400/50 hover:border-blue-400 transition-colors duration-200" />
                            </a>
                          ) : (
                            <span className="text-gray-500 text-sm">No screenshot</span>
                          )}
                        </td>
                        <td className="px-4 py-3">
                          <span className={`inline-block px-2 py-1 rounded-full text-xs font-medium ${
                            payment.status === 'pending' ? 'bg-yellow-500/10 text-yellow-500' :
                            payment.status === 'approved' ? 'bg-green-500/10 text-green-500' :
                            'bg-red-500/10 text-red-500'
                          }`}>
                            {payment.status}
                          </span>
                        </td>
                        <td className="px-4 py-3 text-sm text-gray-400">{new Date(payment.created_at).toLocaleString()}</td>
                      </tr>
                    );
                  })}
                </tbody>
              </table>
            </div>
          </div>
        )}

        {activeTab === 'withdrawals' && (
          <div className="card-hover p-6">
            <div className="flex items-center justify-between mb-6">
              <h2 className="text-2xl font-bold text-white flex items-center space-x-2">
                <TrendingUp className="w-6 h-6 text-purple-400" />
                <span>Withdrawal Requests</span>
              </h2>
              <div className="text-sm text-gray-400">
                Total: {withdrawals.length} requests
              </div>
            </div>
            <div className="overflow-x-auto">
              <table className="min-w-full">
                <thead>
                  <tr className="border-b border-white/20">
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">User ID</th>
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">Email</th>
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">Name</th>
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">Amount</th>
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">Mobile</th>
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">UPI ID</th>
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">Status</th>
                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-300">Notes/Actions</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-white/10">
                  {withdrawals.map((withdrawal) => (
                    <WithdrawalRow 
                      key={withdrawal.id} 
                      withdrawal={withdrawal} 
                      onUpdate={fetchAllData}
                    />
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
