-- Function to get table and column information
create or replace function get_table_info()
returns text
language plpgsql
as $$
declare
    col_info text;
begin
    select string_agg(
        format(
            'Table: %s, Column: %s, Type: %s',
            table_name,
            column_name,
            data_type
        ),
        E'\n'
    )
    into col_info
    from information_schema.columns
    where table_name in ('users_balance');
    
    return col_info;
end;
$$;

-- Add topups column to users_balance table
alter table users_balance add column if not exists topups numeric default 0;

-- Drop existing function first
drop function if exists get_all_user_details();

-- Create diagnostic function to show exact query result structure
create or replace function debug_user_query()
returns table (
    col_name text,
    col_type text
)
language plpgsql
as $$
begin
    return query
    select 
        a.attname::text as col_name,
        format_type(a.atttypid, a.atttypmod)::text as col_type
    from pg_attribute a
    where a.attrelid = (
        select x.oid 
        from (
            select au.id as user_id,
                   au.email as email,
                   (au.raw_user_meta_data->>'full_name')::text as full_name,
                   coalesce(ub.balance, 0::numeric) as balance,
                   coalesce(ub.topups, 0) as topups
            from auth.users au
            left join users_balance ub on au.id = ub.user_id
            limit 0
        ) x
    )
    and a.attnum > 0;
end;
$$;

-- Create new function with corrected types
create function get_all_user_details()
returns table (
  user_id text,
  email text,
  full_name text,
  balance text,
  topups text
)
language plpgsql
security definer
as $$
begin
  return query
  select 
    au.id::text as user_id,
    au.email as email,
    coalesce(au.raw_user_meta_data->>'full_name', '') as full_name,
    coalesce(ub.balance, 0)::text as balance,
    coalesce(ub.topups, 0)::text as topups
  from auth.users au
  left join users_balance ub on au.id = ub.user_id;
end;
$$;

-- Grant execute permission to service_role only
revoke execute on function get_all_user_details() from anon, authenticated;
grant execute on function get_all_user_details() to service_role;
