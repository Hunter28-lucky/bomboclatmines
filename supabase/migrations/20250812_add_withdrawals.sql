-- Create withdrawals table
create table if not exists withdrawals (
    id uuid default uuid_generate_v4() primary key,
    user_id uuid references auth.users(id) not null,
    amount numeric not null check (amount > 0),
    mobile_number text not null,
    upi_id text not null,
    status text not null default 'pending' check (status in ('pending', 'approved', 'rejected')),
    admin_note text,
    requested_at timestamp with time zone default timezone('utc'::text, now()) not null,
    processed_at timestamp with time zone,
    created_at timestamp with time zone default timezone('utc'::text, now()) not null
);

-- RLS Policies for withdrawals
alter table withdrawals enable row level security;

-- Users can read their own withdrawals
create policy "Users can read own withdrawals"
    on withdrawals
    for select
    using (auth.uid() = user_id);

-- Users can create their own withdrawals
create policy "Users can create withdrawals"
    on withdrawals
    for insert
    with check (auth.uid() = user_id);

-- Function to handle withdrawal requests
create or replace function request_withdrawal(
    p_amount numeric,
    p_mobile_number text,
    p_upi_id text
)
returns json
language plpgsql
security definer
as $$
declare
    v_user_id uuid;
    v_balance numeric;
    v_result json;
begin
    -- Get current user ID
    v_user_id := auth.uid();
    if v_user_id is null then
        return json_build_object('success', false, 'message', 'Not authenticated');
    end if;

    -- Get user's current balance
    select balance into v_balance
    from users_balance
    where user_id = v_user_id;

    -- Validate balance
    if v_balance < p_amount then
        return json_build_object('success', false, 'message', 'Insufficient balance');
    end if;

    -- Create withdrawal request
    insert into withdrawals (user_id, amount, mobile_number, upi_id)
    values (v_user_id, p_amount, p_mobile_number, p_upi_id);

    -- Update user balance
    update users_balance
    set balance = balance - p_amount
    where user_id = v_user_id;

    return json_build_object('success', true, 'message', 'Withdrawal request submitted successfully');
end;
$$;

-- Function for admin to process withdrawals
create or replace function admin_process_withdrawal(
    p_withdrawal_id uuid,
    p_status text,
    p_admin_note text default null
)
returns json
language plpgsql
security definer
as $$
declare
    v_withdrawal withdrawals;
begin
    -- Verify status is valid
    if p_status not in ('approved', 'rejected') then
        return json_build_object('success', false, 'message', 'Invalid status');
    end if;

    -- Get withdrawal request
    select * into v_withdrawal
    from withdrawals
    where id = p_withdrawal_id;

    if not found then
        return json_build_object('success', false, 'message', 'Withdrawal request not found');
    end if;

    -- If rejecting, return amount to user's balance
    if p_status = 'rejected' then
        update users_balance
        set balance = balance + v_withdrawal.amount
        where user_id = v_withdrawal.user_id;
    end if;

    -- Update withdrawal status
    update withdrawals
    set status = p_status,
        admin_note = p_admin_note,
        processed_at = now()
    where id = p_withdrawal_id;

    return json_build_object('success', true, 'message', 'Withdrawal processed successfully');
end;
$$;

-- Function to get admin withdrawal list
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

-- Grant permissions
grant execute on function request_withdrawal(numeric, text, text) to authenticated;
grant execute on function admin_process_withdrawal(uuid, text, text) to service_role;
grant execute on function get_all_withdrawals() to service_role;
