import { supabase } from '../supabaseClient';

export interface GameSession {
  id: string;
  user_id: string;
  bet_amount: number;
  grid_size: number;
  bomb_count: number;
  tiles: string; // JSON string of tile positions
  current_winnings: number;
  state: 'betting' | 'playing' | 'trapped' | 'collected';
  created_at: string;
}

// Function to validate and update balance
export async function updateUserBalance(userId: string, newBalance: number, action: 'bet' | 'win' | 'deposit'): Promise<{ success: boolean; error?: string; balance?: number }> {
  const { data: currentData, error: fetchError } = await supabase
    .from('users_balance')
    .select('balance')
    .eq('user_id', userId)
    .single();

  if (fetchError) {
    return { success: false, error: 'Failed to fetch current balance' };
  }

  // Server-side validation
  if (newBalance < 0) {
    return { success: false, error: 'Invalid balance amount' };
  }

  if (action === 'bet' && currentData.balance < newBalance) {
    return { success: false, error: 'Insufficient balance' };
  }

  const { data, error: updateError } = await supabase
    .from('users_balance')
    .update({ balance: newBalance })
    .eq('user_id', userId)
    .select()
    .single();

  if (updateError) {
    return { success: false, error: 'Failed to update balance' };
  }

  return { success: true, balance: data.balance };
}

// Function to create and validate a new game session
export async function createGameSession(
  userId: string,
  betAmount: number,
  gridSize: number,
  bombCount: number
): Promise<{ success: boolean; error?: string; session?: GameSession }> {
  // Validate inputs
  if (betAmount <= 0) return { success: false, error: 'Invalid bet amount' };
  if (gridSize !== 16 && gridSize !== 25 && gridSize !== 36) return { success: false, error: 'Invalid grid size' };
  if (bombCount <= 0 || bombCount >= gridSize * 0.4) return { success: false, error: 'Invalid bomb count' };

  // Check user's balance
  const { success, error, balance } = await updateUserBalance(userId, betAmount, 'bet');
  if (!success) return { success: false, error };

  // Generate secure tile positions
  const tiles = generateSecureTiles(gridSize, bombCount);

  const { data, error: insertError } = await supabase
    .from('game_sessions')
    .insert([{
      user_id: userId,
      bet_amount: betAmount,
      grid_size: gridSize,
      bomb_count: bombCount,
      tiles: JSON.stringify(tiles),
      current_winnings: 0,
      state: 'playing'
    }])
    .select()
    .single();

  if (insertError) {
    return { success: false, error: 'Failed to create game session' };
  }

  return { success: true, session: data as GameSession };
}

// Function to validate and process tile reveal
export async function revealTile(
  sessionId: string,
  userId: string,
  tileIndex: number
): Promise<{ success: boolean; error?: string; result?: { isReward: boolean; winnings: number } }> {
  // Fetch game session
  const { data: session, error: fetchError } = await supabase
    .from('game_sessions')
    .select('*')
    .eq('id', sessionId)
    .eq('user_id', userId)
    .single();

  if (fetchError || !session) {
    return { success: false, error: 'Invalid game session' };
  }

  // Validate game state
  if (session.state !== 'playing') {
    return { success: false, error: 'Game is not in playing state' };
  }

  // Validate tile reveal
  const tiles = JSON.parse(session.tiles);
  if (tileIndex < 0 || tileIndex >= tiles.length) {
    return { success: false, error: 'Invalid tile index' };
  }

  const tile = tiles[tileIndex];
  if (tile.revealed) {
    return { success: false, error: 'Tile already revealed' };
  }

  // Update tile state
  tile.revealed = true;
  tiles[tileIndex] = tile;

  // Calculate winnings if it's a reward
  let winnings = session.current_winnings;
  if (!tile.isBomb) {
    const revealedCount = tiles.filter(t => t.revealed).length;
    const multiplier = calculateMultiplier(revealedCount, session.grid_size, session.bomb_count);
    winnings = session.bet_amount * multiplier;
  }

  // Update game session
  const { error: updateError } = await supabase
    .from('game_sessions')
    .update({
      tiles: JSON.stringify(tiles),
      current_winnings: winnings,
      state: tile.isBomb ? 'trapped' : 'playing'
    })
    .eq('id', sessionId)
    .eq('user_id', userId);

  if (updateError) {
    return { success: false, error: 'Failed to update game session' };
  }

  return {
    success: true,
    result: {
      isReward: !tile.isBomb,
      winnings
    }
  };
}

// Helper function to calculate multiplier (moved to server-side)
function calculateMultiplier(tilesRevealed: number, totalTiles: number, bombCount: number): number {
  const safeTiles = totalTiles - bombCount;
  const remainingSafeTiles = safeTiles - tilesRevealed;
  const remainingTiles = totalTiles - tilesRevealed;
  
  if (remainingSafeTiles <= 0) return 1;
  
  const baseMultiplier = remainingTiles / remainingSafeTiles;
  const HOUSE_EDGE_FACTOR = 0.97; // 3% house edge
  return Math.max(1.05, baseMultiplier * HOUSE_EDGE_FACTOR);
}

// Helper function to generate secure tile positions
function generateSecureTiles(gridSize: number, bombCount: number) {
  const tiles = Array(gridSize).fill(null).map((_, i) => ({
    id: i,
    revealed: false,
    isBomb: false
  }));

  // Use crypto for secure random number generation
  const getSecureRandom = () => {
    const array = new Uint32Array(1);
    crypto.getRandomValues(array);
    return array[0] / (0xffffffff + 1);
  };

  let remainingBombs = bombCount;
  while (remainingBombs > 0) {
    const position = Math.floor(getSecureRandom() * gridSize);
    if (!tiles[position].isBomb) {
      tiles[position].isBomb = true;
      remainingBombs--;
    }
  }

  return tiles;
}
