-- Add topups column to users_balance table
alter table users_balance add column if not exists topups integer default 0;

-- Drop existing function first
drop function if exists get_all_user_details();

-- Create new function with corrected types
create function get_all_user_details()
returns table (
  user_id uuid,
  email text,
  full_name text,
  balance numeric,
  topups integer
)
language plpgsql
security definer
as $$
begin
  return query
  select 
    au.id as user_id,
    au.email as email,
    coalesce(au.raw_user_meta_data->>'full_name', '') as full_name,
    coalesce(ub.balance, 0::numeric) as balance,
    coalesce(ub.topups, 0) as topups
  from auth.users au
  left join users_balance ub on au.id = ub.user_id;
end;
$$;

-- Grant execute permission to service_role only
revoke execute on function get_all_user_details() from anon, authenticated;
grant execute on function get_all_user_details() to service_role;
