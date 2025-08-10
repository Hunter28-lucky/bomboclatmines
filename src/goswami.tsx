import { useEffect, useState } from 'react';
import { supabase } from './supabaseClient';
import Login from './Login';

const ADMIN_EMAIL = 'krrishyogi18@gmail.com';

type UserBalance = {
  user_id: string;
  balance: number;
  topups?: number;
};

type Payment = {
  id: number;
  site_id: string;
  mobile_number: string;
  utr_number: string;
  screenshot_url?: string;
  status: string;
  submitted_at: string;
};

export default function AdminPage() {
  const [users, setUsers] = useState<UserBalance[]>([]);
  const [payments, setPayments] = useState<Payment[]>([]);
  const [loading, setLoading] = useState(true);
  const [authChecked, setAuthChecked] = useState(false);
  const [user, setUser] = useState<any>(null);

  useEffect(() => {
    supabase.auth.getSession().then(({ data }) => {
      const currentUser = data.session?.user;
      setUser(currentUser);
      setAuthChecked(true);
      if (currentUser && currentUser.email === ADMIN_EMAIL) {
        fetchData();
      }
    });
    // Listen for auth state changes
    const { data: listener } = supabase.auth.onAuthStateChange((_event, session) => {
      const currentUser = session?.user;
      setUser(currentUser);
      setAuthChecked(true);
      if (currentUser && currentUser.email === ADMIN_EMAIL) {
        fetchData();
      }
    });
    return () => {
      listener?.subscription.unsubscribe();
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  async function fetchData() {
    setLoading(true);
    // Fetch all users
    const { data: userData } = await supabase.from('users_balance').select('*');
    setUsers((userData as UserBalance[]) || []);
    // Fetch all payments
    const { data: paymentData } = await supabase.from('payments').select('*');
    setPayments((paymentData as Payment[]) || []);
    setLoading(false);
  }

  if (!authChecked) return <div className="flex justify-center items-center h-screen text-xl">Checking authentication...</div>;
  if (!user || user.email !== ADMIN_EMAIL) {
    return <Login onLogin={async () => {
      const { data } = await supabase.auth.getSession();
      setUser(data.session?.user);
      setAuthChecked(true);
      if (data.session?.user && data.session.user.email === ADMIN_EMAIL) {
        fetchData();
      }
    }} />;
  }
  if (loading) return <div className="flex justify-center items-center h-screen text-xl">Loading...</div>;

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
                <th className="px-4 py-2">Balance</th>
                <th className="px-4 py-2">Topups</th>
              </tr>
            </thead>
            <tbody>
              {users.map(u => (
                <tr key={u.user_id} className="border-b border-gray-700">
                  <td className="px-4 py-2">
                    <img src={`https://ui-avatars.com/api/?name=${u.user_id}&background=0ea5e9&color=fff`} alt="avatar" className="w-10 h-10 rounded-full" />
                  </td>
                  <td className="px-4 py-2">{u.user_id}</td>
                  <td className="px-4 py-2 font-bold text-green-400">₹{u.balance}</td>
                  <td className="px-4 py-2">{u.topups || 0}</td>
                </tr>
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
                <th className="px-4 py-2">Mobile</th>
                <th className="px-4 py-2">UTR</th>
                <th className="px-4 py-2">Screenshot</th>
                <th className="px-4 py-2">Status</th>
                <th className="px-4 py-2">Submitted At</th>
              </tr>
            </thead>
            <tbody>
              {payments.map(p => (
                <tr key={p.id} className="border-b border-gray-700">
                  <td className="px-4 py-2">{p.site_id}</td>
                  <td className="px-4 py-2">{p.mobile_number}</td>
                  <td className="px-4 py-2">{p.utr_number}</td>
                  <td className="px-4 py-2">
                    {p.screenshot_url ? (
                      <a href={p.screenshot_url} target="_blank" rel="noopener noreferrer">
                        <img src={p.screenshot_url} alt="screenshot" className="w-16 h-16 rounded-lg border border-blue-400" />
                      </a>
                    ) : 'No screenshot'}
                  </td>
                  <td className="px-4 py-2 font-bold text-yellow-400">{p.status}</td>
                  <td className="px-4 py-2">{new Date(p.submitted_at).toLocaleString()}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </section>
    </div>
  );
}
