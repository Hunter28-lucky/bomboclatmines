import { useEffect, useState } from 'react';
import { supabase } from '../supabaseClient';
import UserRow from './UserRow';

const ADMIN_EMAIL = 'krrishyogi18@gmail.com';

type UserBalance = {
  user_id: string;
  balance: number;
  topups?: number;
  email?: string;
  name?: string;
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

  useEffect(() => {
    supabase.auth.getUser().then(({ data }) => {
      if (!data?.user || data.user.email !== ADMIN_EMAIL) {
        window.location.replace('/');
      } else {
        fetchData();
      }
    });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  async function fetchData() {
    setLoading(true);
    // Fetch all users with email and name from auth.users
    const { data: userData } = await supabase.from('users_balance').select('*');
    let usersWithMeta: UserBalance[] = [];
    if (userData && Array.isArray(userData)) {
      // Fetch user metadata from auth.users
      const { data: authUsers } = await supabase.from('users').select('id,email,user_metadata');
      usersWithMeta = userData.map((u: any) => {
        const authUser = authUsers?.find((au: any) => au.id === u.user_id);
        return {
          ...u,
          email: authUser?.email || u.user_id,
          name: authUser?.user_metadata?.full_name || authUser?.user_metadata?.name || '',
        };
      });
    }
    setUsers(usersWithMeta);
    // Fetch all payments
    const { data: paymentData } = await supabase.from('payments').select('*');
    setPayments((paymentData as Payment[]) || []);
    setLoading(false);
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
                <th className="px-4 py-2">Name</th>
                <th className="px-4 py-2">Balance</th>
                <th className="px-4 py-2">Topups</th>
              </tr>
            </thead>
            <tbody>
              {users.map(u => (
                <UserRow key={u.user_id} user={u} onUpdate={fetchData} />
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
              {payments.map(p => {
                const userMeta = users.find(u => u.user_id === p.site_id);
                return (
                  <tr key={p.id} className="border-b border-gray-700">
                    <td className="px-4 py-2">{p.site_id}</td>
                    <td className="px-4 py-2">{userMeta?.email || p.site_id}</td>
                    <td className="px-4 py-2">{userMeta?.name || ''}</td>
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
                );
              })}
            </tbody>
          </table>
        </div>
      </section>
    </div>
  );
}
