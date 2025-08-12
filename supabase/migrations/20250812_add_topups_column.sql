-- Add topups column to users_balance table
alter table users_balance add column if not exists topups bigint default 0;

-- Update get_all_user_details function to use the new column
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
  return query
  select 
    au.id as user_id,
    au.email as email,
    (au.raw_user_meta_data->>'full_name')::text as full_name,
    coalesce(ub.balance, 0) as balance,
    coalesce(ub.topups, 0) as topups
  from auth.users au
  left join users_balance ub on au.id = ub.user_id;
end;
$$;

-- Grant execute permission to service_role only
revoke execute on function get_all_user_details() from anon, authenticated;
grant execute on function get_all_user_details() to service_role;
