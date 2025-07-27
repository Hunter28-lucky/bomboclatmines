-- Enable Row Level Security
alter table users_balance enable row level security;
alter table game_sessions enable row level security;

-- Create game_sessions table
create table if not exists game_sessions (
  id uuid default uuid_generate_v4() primary key,
  user_id uuid references auth.users(id) not null,
  bet_amount integer not null check (bet_amount > 0),
  grid_size integer not null check (grid_size in (16, 25, 36)),
  bomb_count integer not null,
  tiles jsonb not null,
  current_winnings numeric(10, 2) not null default 0,
  state text not null check (state in ('betting', 'playing', 'trapped', 'collected')),
  created_at timestamp with time zone default timezone('utc'::text, now()) not null,
  check (bomb_count > 0 and bomb_count < grid_size * 0.4)
);

-- RLS Policies for users_balance
create policy "Users can read own balance"
  on users_balance
  for select
  using (auth.uid() = user_id);

create policy "Users can update own balance"
  on users_balance
  for update
  using (auth.uid() = user_id);

-- RLS Policies for game_sessions
create policy "Users can read own game sessions"
  on game_sessions
  for select
  using (auth.uid() = user_id);

create policy "Users can create own game sessions"
  on game_sessions
  for insert
  with check (auth.uid() = user_id);

create policy "Users can update own game sessions"
  on game_sessions
  for update
  using (auth.uid() = user_id);

-- Function to initialize user balance
create or replace function public.handle_new_user()
returns trigger
language plpgsql
security definer
as $$
begin
  insert into public.users_balance (user_id, balance)
  values (new.id, 500);
  return new;
end;
$$;

-- Trigger to initialize user balance on signup
create trigger on_auth_user_created
  after insert on auth.users
  for each row execute function public.handle_new_user();

-- Function to validate balance updates
create or replace function public.validate_balance_update()
returns trigger
language plpgsql
as $$
begin
  if new.balance < 0 then
    raise exception 'Balance cannot be negative';
  end if;
  return new;
end;
$$;

-- Trigger for balance validation
create trigger validate_balance_before_update
  before update on public.users_balance
  for each row execute function public.validate_balance_update();
