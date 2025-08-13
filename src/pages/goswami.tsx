import { useState, useEffect } from 'react';
import UserRow from '../components/UserRow';
import WithdrawalRow from '../components/WithdrawalRow';
import { supabase, supabaseAdmin } from '../supabaseClient';

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

export default function AdminPage() {
  const [users, setUsers] = useState<UserBalance[]>([]);
  const [payments, setPayments] = useState<Payment[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);
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
};  const [withdrawals, setWithdrawals] = useState<Withdrawal[]>([]);

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
      const { data: authUsers, error: authError } = await supabaseAdmin
        .auth.admin.listUsers();

      if (authError) {
        console.error('Auth users error:', authError);
        setError('Failed to fetch auth users: ' + authError.message);
        return;
      }

      // Combine user data with auth data
      const enrichedUserData = (userData as UserBalance[])?.map((user: UserBalance) => {
        const authUser = (authUsers.users as Array<{ id: string; email?: string; user_metadata?: { full_name?: string } }> ).find((au) => au.id === user.user_id);
        return {
          ...user,
          email: authUser?.email || user.email || '',
          full_name: authUser?.user_metadata?.full_name || user.full_name || ''
        };
      }) || [];

      setUsers(enrichedUserData);

      // Fetch all payments
      const { data: paymentData, error: paymentError } = await supabaseAdmin
        .from('payments')
        .select('*')
        .order('created_at', { ascending: false });

      if (paymentError) throw paymentError;
      setPayments(paymentData || []);
    } catch (err) {
      console.error('Error fetching data:', err);
      setError(err instanceof Error ? err.message : 'Failed to fetch data');
    } finally {
      setLoading(false);
    }
  }

  // Keep track of the subscription
  const [withdrawalChannel, setWithdrawalChannel] = useState<ReturnType<typeof supabaseAdmin.channel> | null>(null);

  useEffect(() => {
    // Clean up the subscription when component unmounts
    return () => {
      if (withdrawalChannel) {
        supabaseAdmin.removeChannel(withdrawalChannel);
      }
    };
  }, [withdrawalChannel]);

  async function fetchWithdrawals() {
    try {
      console.log('Fetching withdrawals...');
      const { data, error } = await supabaseAdmin.rpc('get_all_withdrawals');
      
      if (error) {
        console.error('Error fetching withdrawals:', error);
        setError('Failed to fetch withdrawals: ' + error.message);
        setWithdrawals([]);
        return;
      }
      
      if (!data) {
        console.log('No withdrawals found');
        setWithdrawals([]);
        return;
      }

      // If data is an array, use it directly. If not, wrap in array
      const withdrawalArray = Array.isArray(data) ? data : (data ? [data] : []);
      const formattedWithdrawals = withdrawalArray.map((w: any) => ({
        id: w.id,
        user_id: w.user_id,
        email: w.email,
        full_name: w.full_name,
        amount: w.amount ? w.amount.toString() : '0',
        mobile_number: w.mobile_number,
        upi_id: w.upi_id,
        status: w.status,
        admin_note: w.admin_note,
        requested_at: w.requested_at,
        processed_at: w.processed_at,
        created_at: w.created_at
      }));

      console.log('Withdrawals fetched:', formattedWithdrawals);
      setWithdrawals(formattedWithdrawals);

      // Set up real-time subscription if not already set
      if (!withdrawalChannel) {
        const channel = supabaseAdmin
          .channel('admin-withdrawal-updates')
          .on('postgres_changes',
            { event: '*', schema: 'public', table: 'withdrawals' },
            () => {
              fetchWithdrawals(); // Refresh the list when there are changes
            }
          )
          .subscribe();
        
        setWithdrawalChannel(channel);
      }
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

  if (loading) {
    return (
      <div className="flex justify-center items-center h-screen text-xl">
        Loading...
      </div>
    );
  }

  if (error) {
    return (
      <div className="flex justify-center items-center h-screen">
        <div className="bg-red-500/10 text-red-500 px-4 py-2 rounded-lg">
          {error}
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-gray-900 to-blue-900 text-white p-4">
      <h1 className="text-3xl font-bold mb-6 text-center">Admin Dashboard</h1>
      <section className="mb-10">
        <h2 className="text-2xl font-semibold mb-4">Users</h2>
        <div className="overflow-x-auto">
          <table className="min-w-full bg-gray-800 rounded-lg shadow-lg">
            <thead>
              <tr>
                <th className="px-4 py-2">Profile</th>
                <th className="px-4 py-2">Email</th>
                <th className="px-4 py-2">Name</th>
                <th className="px-4 py-2">Balance</th>
                <th className="px-4 py-2">Topups</th>
                <th className="px-4 py-2">Actions</th>
              </tr>
            </thead>
            <tbody>
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
      </section>
      <section>
        <h2 className="text-2xl font-semibold mb-4">Payments</h2>
        <div className="overflow-x-auto">
          <table className="min-w-full bg-gray-800 rounded-lg shadow-lg">
            <thead>
              <tr>
                <th className="px-4 py-2">User</th>
                <th className="px-4 py-2">Email</th>
                <th className="px-4 py-2">Name</th>
                <th className="px-4 py-2">Mobile</th>
                <th className="px-4 py-2">UTR</th>
                <th className="px-4 py-2">Screenshot</th>
                <th className="px-4 py-2">Status</th>
                <th className="px-4 py-2">Submitted At</th>
              </tr>
            </thead>
            <tbody>
              {payments.map((payment: Payment) => {
                const userMeta = users.find((u: UserBalance) => u.user_id === payment.site_id);
                return (
                  <tr key={payment.id} className="border-b border-gray-700">
                    <td className="px-4 py-2">{payment.site_id}</td>
                    <td className="px-4 py-2">{userMeta?.email || ''}</td>
                    <td className="px-4 py-2">{userMeta?.full_name || ''}</td>
                    <td className="px-4 py-2">{payment.mobile_number}</td>
                    <td className="px-4 py-2">{payment.utr_number}</td>
                    <td className="px-4 py-2">
                      {payment.screenshot_url ? (
                        <a href={payment.screenshot_url} target="_blank" rel="noopener noreferrer">
                          <img src={payment.screenshot_url} alt="screenshot" className="w-16 h-16 rounded-lg border border-blue-400" />
                        </a>
                      ) : 'No screenshot'}
                    </td>
                    <td className="px-4 py-2 font-bold text-yellow-400">{payment.status}</td>
                    <td className="px-4 py-2">{new Date(payment.created_at).toLocaleString()}</td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        </div>
      </section>

      <section className="mb-10">
        <h2 className="text-2xl font-semibold mb-4">Withdrawal Requests</h2>
        <div className="overflow-x-auto">
          <table className="min-w-full bg-gray-800 rounded-lg shadow-lg">
            <thead>
              <tr>
                <th className="px-4 py-2">User ID</th>
                <th className="px-4 py-2">Email</th>
                <th className="px-4 py-2">Name</th>
                <th className="px-4 py-2">Amount</th>
                <th className="px-4 py-2">Mobile</th>
                <th className="px-4 py-2">UPI ID</th>
                <th className="px-4 py-2">Status</th>
                <th className="px-4 py-2">Notes/Actions</th>
              </tr>
            </thead>
            <tbody>
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
      </section>
    </div>
  );
}
