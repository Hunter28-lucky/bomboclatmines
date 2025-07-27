export interface Tile {
  id: number;
  revealed: boolean;
  isReward: boolean;
}

export interface GameSettings {
  gridSize: number;
  bombCount: number;
  betAmount: number;
}

export type GameState = 'betting' | 'playing' | 'trapped' | 'collected';
