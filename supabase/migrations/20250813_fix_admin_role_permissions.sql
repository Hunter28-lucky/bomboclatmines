-- Revoke any existing permissions
revoke execute on function get_all_withdrawals() from public;
revoke execute on function get_all_withdrawals() from authenticated;

-- Drop and recreate the function with proper admin access
drop function if exists get_all_withdrawals();

create or replace function get_all_withdrawals()
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
    processed_at timestamp with time zone,
    created_at timestamp with time zone
)
language plpgsql
security definer
set search_path = public
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
        w.processed_at,
        w.created_at
    from withdrawals w
    join auth.users au on au.id = w.user_id
    order by w.created_at desc;
end;
$$;

-- Grant proper permissions
grant usage on schema public to service_role;
grant all on all tables in schema public to service_role;
grant all on all sequences in schema public to service_role;
grant all on all routines in schema public to service_role;

-- Specifically grant execute on the admin function
grant execute on function get_all_withdrawals() to service_role;
grant execute on function get_all_withdrawals() to authenticated;
