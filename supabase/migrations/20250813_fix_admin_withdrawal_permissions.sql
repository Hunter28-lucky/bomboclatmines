-- Drop existing function to recreate with proper permissions
drop function if exists get_all_withdrawals();

-- Recreate the function with proper permissions
create function get_all_withdrawals()
returns table (
    id uuid,
    user_id text,
    email text,
    full_name text,
    amount numeric,
    mobile_number text,
    upi_id text,
    status text,
    admin_note text,
    requested_at timestamp with time zone,
    processed_at timestamp with time zone
)
language plpgsql
security definer
as $$
begin
    return query
    select 
        w.id,
        au.id::text as user_id,
        au.email,
        coalesce(au.raw_user_meta_data->>'full_name', '') as full_name,
        w.amount,
        w.mobile_number,
        w.upi_id,
        w.status,
        w.admin_note,
        w.requested_at,
        w.processed_at
    from withdrawals w
    join auth.users au on au.id = w.user_id
    order by w.requested_at desc;
end;
$$;

-- Revoke all existing permissions
revoke all on function get_all_withdrawals() from public;
revoke all on function get_all_withdrawals() from authenticated;
revoke all on function get_all_withdrawals() from service_role;

-- Grant execute permission to anon and service_role
grant execute on function get_all_withdrawals() to anon;
grant execute on function get_all_withdrawals() to service_role;
