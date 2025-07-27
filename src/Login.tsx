import React, { useState } from 'react';
import { supabase } from './supabaseClient';

export default function Login({ onLogin }: { onLogin: () => void }) {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const handleSignUp = async () => {
    setLoading(true);
    setError(null);
    const { error } = await supabase.auth.signUp({ email, password });
    if (error) setError(error.message);
    setLoading(false);
  };

  const handleSignIn = async () => {
    setLoading(true);
    setError(null);
    const { error } = await supabase.auth.signInWithPassword({ email, password });
    if (error) setError(error.message);
    setLoading(false);
  };

  const handleGoogle = async () => {
    setLoading(true);
    setError(null);
    const { error } = await supabase.auth.signInWithOAuth({ provider: 'google' });
    if (error) setError(error.message);
    setLoading(false);
  };

  return (
    <div className="flex flex-col items-center justify-center min-h-screen bg-gradient-to-br from-gray-900 to-blue-900">
      <div className="bg-gray-800 p-8 rounded-xl shadow-xl w-full max-w-sm">
        <h2 className="text-2xl font-bold text-cyan-400 mb-6 text-center">Sign in to Play</h2>
        <input
          className="w-full mb-3 p-3 rounded bg-gray-700 text-white focus:outline-none"
          type="email"
          placeholder="Email"
          value={email}
          onChange={e => setEmail(e.target.value)}
        />
        <input
          className="w-full mb-4 p-3 rounded bg-gray-700 text-white focus:outline-none"
          type="password"
          placeholder="Password"
          value={password}
          onChange={e => setPassword(e.target.value)}
        />
        {error && <div className="text-red-400 mb-2 text-sm">{error}</div>}
        <button
          className="w-full py-3 mb-2 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-lg font-bold text-white hover:from-cyan-600 hover:to-blue-700 transition-all"
          onClick={handleSignIn}
          disabled={loading}
        >
          {loading ? 'Signing in...' : 'Sign In'}
        </button>
        <button
          className="w-full py-3 mb-2 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg font-bold text-white hover:from-green-600 hover:to-emerald-700 transition-all"
          onClick={handleSignUp}
          disabled={loading}
        >
          {loading ? 'Signing up...' : 'Sign Up'}
        </button>
        <button
          className="w-full py-3 bg-gradient-to-r from-red-500 to-yellow-500 rounded-lg font-bold text-white hover:from-red-600 hover:to-yellow-600 transition-all flex items-center justify-center gap-2"
          onClick={handleGoogle}
          disabled={loading}
        >
          <svg width="20" height="20" viewBox="0 0 48 48"><g><path fill="#4285F4" d="M43.611 20.083H42V20H24v8h11.303C34.73 32.082 29.818 35 24 35c-6.627 0-12-5.373-12-12s5.373-12 12-12c2.803 0 5.377.99 7.409 2.626l6.591-6.591C34.583 5.163 29.584 3 24 3 12.954 3 4 11.954 4 23s8.954 20 20 20c11.045 0 19.799-8.954 19.799-20 0-1.341-.138-2.651-.377-3.917z"/><path fill="#34A853" d="M6.306 14.691l6.571 4.819C14.655 16.108 19.008 13 24 13c2.803 0 5.377.99 7.409 2.626l6.591-6.591C34.583 5.163 29.584 3 24 3c-7.732 0-14.41 4.41-17.694 10.691z"/><path fill="#FBBC05" d="M24 43c5.798 0 10.672-1.924 14.229-5.217l-6.569-5.389C29.818 35 24 35 24 35c-5.818 0-10.73-2.918-13.303-7.083l-6.591 6.591C9.59 42.09 16.268 43 24 43z"/><path fill="#EA4335" d="M43.611 20.083H42V20H24v8h11.303C34.73 32.082 29.818 35 24 35c-6.627 0-12-5.373-12-12s5.373-12 12-12c2.803 0 5.377.99 7.409 2.626l6.591-6.591C34.583 5.163 29.584 3 24 3c-7.732 0-14.41 4.41-17.694 10.691z"/></g></svg>
          Sign in with Google
        </button>
      </div>
    </div>
  );
}
