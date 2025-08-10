-- Enable RLS
alter table users_balance enable row level security;

-- Create policy to allow admin to update any balance
create policy "Admin can update any balance"
on users_balance
for update
using (
  -- Check if user is admin
  (select email = 'krrishyogi18@gmail.com' from auth.users where id = auth.uid())
);

-- Create admin function to update balance
create or replace function admin_update_balance(p_user_id uuid, p_balance bigint)
returns void
language plpgsql
security definer
as $$
begin
  -- Verify caller is admin
  if not exists (
    select 1 from auth.users 
    where id = auth.uid() 
    and email = 'krrishyogi18@gmail.com'
  ) then
    raise exception 'Not authorized';
  end if;

  -- Update balance
  update users_balance 
  set balance = p_balance
  where user_id = p_user_id;

  -- Insert if not exists
  if not found then
    insert into users_balance (user_id, balance)
    values (p_user_id, p_balance);
  end if;
end;
$$;

-- Create function to get all user details
create or replace function get_all_user_details()
returns table (
  user_id uuid,
  email text,
  full_name text,
  balance bigint,
  topups bigint
)
language plpgsql
security definer
as $$
begin
  -- Verify caller is admin
  if not exists (
    select 1 from auth.users 
    where id = auth.uid() 
    and email = 'krrishyogi18@gmail.com'
  ) then
    raise exception 'Not authorized';
  end if;

  return query
  select 
    au.id as user_id,
    au.email,
    coalesce(au.raw_user_meta_data->>'full_name', au.raw_user_meta_data->>'name', '') as full_name,
    coalesce(ub.balance, 0) as balance,
    coalesce(ub.topups, 0) as topups
  from auth.users au
  left join users_balance ub on au.id = ub.user_id;
end;
$$;

-- Grant execute permission to authenticated users
grant execute on function admin_update_balance(uuid, bigint) to authenticated;
grant execute on function get_all_user_details() to authenticated;
