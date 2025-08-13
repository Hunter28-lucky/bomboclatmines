-- Function to get user's withdrawal history
create or replace function get_withdrawal_history()
returns table (
    id uuid,
    amount numeric,
    upi_id text,
    mobile_number text,
    status text,
    admin_note text,
    requested_at timestamp with time zone,
    processed_at timestamp with time zone,
    created_at timestamp with time zone
)
language plpgsql
security definer
as $$
begin
    return query
    select 
        w.id,
        w.amount,
        w.upi_id,
        w.mobile_number,
        w.status,
        w.admin_note,
        w.requested_at,
        w.processed_at,
        w.created_at
    from withdrawals w
    where w.user_id = auth.uid()
    order by w.created_at desc;
end;
$$;

-- Grant permission to authenticated users
grant execute on function get_withdrawal_history() to authenticated;
