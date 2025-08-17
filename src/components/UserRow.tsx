import { useState } from 'react';
import { supabase } from '../supabaseClient';
import { Edit3, Save, X, User } from 'lucide-react';

type UserBalance = {
  user_id: string;
  email: string | null;
  full_name: string | null;
  balance: number;
  topups: number | null;
};

export default function UserRow({ user, onUpdate }: { user: UserBalance, onUpdate: () => void }) {
  const [editing, setEditing] = useState(false);
  const [newBalance, setNewBalance] = useState(user.balance);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function handleSave() {
    setLoading(true);
    setError(null);
    
    // Update balance using secure RPC function
    const { error } = await supabase.rpc('admin_update_balance', {
      p_user_id: user.user_id,
      p_balance: newBalance
    });

    setLoading(false);
    if (error) {
      setError(error.message);
    } else {
      setEditing(false);
      onUpdate();
    }
  }

  return (
    <tr className="hover:bg-white/5 transition-colors duration-200">
      <td className="px-4 py-3">
        <div className="flex items-center space-x-3">
          <img 
            src={`https://ui-avatars.com/api/?name=${user.full_name || user.email}&background=0ea5e9&color=fff`} 
            alt="avatar" 
            className="w-10 h-10 rounded-full border-2 border-white/20" 
          />
          <div className="flex-1 min-w-0">
            <p className="text-sm font-medium text-white truncate">{user.full_name || 'Unknown'}</p>
            <p className="text-xs text-gray-400 truncate">{user.email}</p>
          </div>
        </div>
      </td>
      <td className="px-4 py-3">
        <p className="text-sm text-gray-300 truncate">{user.email}</p>
      </td>
      <td className="px-4 py-3">
        <p className="text-sm text-white font-medium">{user.full_name || 'N/A'}</p>
      </td>
      <td className="px-4 py-3">
        {editing ? (
          <div className="flex items-center space-x-2">
            <input
              type="number"
              value={newBalance}
              onChange={e => setNewBalance(Number(e.target.value))}
              className="w-24 px-3 py-2 rounded-lg bg-slate-700/50 text-white border border-blue-400/50 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all duration-200"
              min="0"
            />
            <span className="text-sm text-gray-400">₹</span>
          </div>
        ) : (
          <div className="flex items-center space-x-2">
            <span className="text-lg font-bold text-gradient-success">₹{user.balance.toLocaleString()}</span>
          </div>
        )}
      </td>
      <td className="px-4 py-3">
        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-500/10 text-purple-400">
          ₹{(user.topups || 0).toLocaleString()}
        </span>
      </td>
      <td className="px-4 py-3">
        {editing ? (
          <div className="flex items-center space-x-2">
            <button
              className="inline-flex items-center px-3 py-2 bg-green-500 hover:bg-green-600 rounded-lg text-white text-sm font-medium transition-all duration-200 hover:shadow-lg hover:shadow-green-500/25 disabled:opacity-50 disabled:cursor-not-allowed"
              onClick={handleSave}
              disabled={loading}
            >
              {loading ? (
                <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
              ) : (
                <>
                  <Save className="w-4 h-4 mr-1" />
                  Save
                </>
              )}
            </button>
            <button
              className="inline-flex items-center px-3 py-2 bg-slate-600 hover:bg-slate-700 rounded-lg text-white text-sm font-medium transition-all duration-200 hover:shadow-lg"
              onClick={() => {
                setEditing(false);
                setNewBalance(user.balance);
                setError(null);
              }}
            >
              <X className="w-4 h-4 mr-1" />
              Cancel
            </button>
          </div>
        ) : (
          <button
            className="inline-flex items-center px-3 py-2 bg-blue-500 hover:bg-blue-600 rounded-lg text-white text-sm font-medium transition-all duration-200 hover:shadow-lg hover:shadow-blue-500/25"
            onClick={() => setEditing(true)}
          >
            <Edit3 className="w-4 h-4 mr-1" />
            Edit
          </button>
        )}
        {error && (
          <div className="mt-2 p-2 bg-red-500/10 border border-red-500/30 rounded-lg">
            <p className="text-red-400 text-xs">{error}</p>
          </div>
        )}
      </td>
    </tr>
  );
}
