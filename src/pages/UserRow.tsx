import { useState } from 'react';
import { supabaseAdmin } from '../supabaseClient';

type UserBalance = {
  user_id: string;
  balance: number;
  topups?: number;
  email?: string;
  name?: string;
};

export default function UserRow({ user, onUpdate }: { user: UserBalance, onUpdate: () => void }) {
  const [editing, setEditing] = useState(false);
  const [newBalance, setNewBalance] = useState(user.balance);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function handleSave() {
    setLoading(true);
    setError(null);
    // Use the admin client for unrestricted access
    const { error } = await supabaseAdmin
      .from('users_balance')
      .update({ balance: newBalance })
      .eq('user_id', user.user_id);

    setLoading(false);
    if (error) {
      setError(error.message);
    } else {
      setEditing(false);
      onUpdate();
    }
  }

  return (
    <tr className="border-b border-gray-700">
      <td className="px-4 py-2">
        <img src={`https://ui-avatars.com/api/?name=${user.name || user.user_id}&background=0ea5e9&color=fff`} alt="avatar" className="w-10 h-10 rounded-full" />
      </td>
      <td className="px-4 py-2">{user.email || user.user_id}</td>
      <td className="px-4 py-2">{user.name || ''}</td>
      <td className="px-4 py-2 font-bold text-green-400">
        {editing ? (
          <input
            type="number"
            value={newBalance}
            onChange={e => setNewBalance(Number(e.target.value))}
            className="w-24 px-2 py-1 rounded bg-gray-700 text-white border border-blue-400 focus:outline-none"
          />
        ) : (
          <>₹{user.balance}</>
        )}
      </td>
      <td className="px-4 py-2">{user.topups || 0}</td>
      <td className="px-4 py-2">
        {editing ? (
          <>
            <button
              className="px-3 py-1 bg-green-500 rounded text-white font-bold mr-2"
              onClick={handleSave}
              disabled={loading}
            >
              {loading ? 'Saving...' : 'Save'}
            </button>
            <button
              className="px-3 py-1 bg-red-500 rounded text-white font-bold"
              onClick={() => {
                setEditing(false);
                setNewBalance(user.balance);
              }}
            >
              Cancel
            </button>
          </>
        ) : (
          <button
            className="px-3 py-1 bg-blue-500 rounded text-white font-bold"
            onClick={() => setEditing(true)}
          >
            Edit
          </button>
        )}
        {error && <p className="text-red-500 text-sm mt-1">{error}</p>}
      </td>
    </tr>
  );
}
