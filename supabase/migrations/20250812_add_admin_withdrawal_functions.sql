-- Function for admins to get all withdrawals with user details
CREATE OR REPLACE FUNCTION public.get_all_withdrawals()
RETURNS TABLE (
    id UUID,
    user_id UUID,
    email TEXT,
    full_name TEXT,
    amount DECIMAL,
    upi_id TEXT,
    status TEXT,
    created_at TIMESTAMPTZ,
    processed_at TIMESTAMPTZ,
    processed_by UUID,
    notes TEXT
)
LANGUAGE plpgsql
SECURITY DEFINER
AS $$
BEGIN
    -- Verify admin status
    IF NOT EXISTS (
        SELECT 1 FROM public.admin_users 
        WHERE user_id = auth.uid()
    ) THEN
        RAISE EXCEPTION 'Unauthorized';
    END IF;

    RETURN QUERY
    SELECT 
        w.id,
        w.user_id,
        u.email,
        u.raw_user_meta_data->>'full_name' as full_name,
        w.amount,
        w.upi_id,
        w.status,
        w.created_at,
        w.processed_at,
        w.processed_by,
        w.notes
    FROM public.withdrawals w
    LEFT JOIN auth.users u ON u.id = w.user_id
    ORDER BY w.created_at DESC;
END;
$$;

-- Grant execute permission to authenticated users (the function's SECURITY DEFINER will handle admin check)
GRANT EXECUTE ON FUNCTION public.get_all_withdrawals TO authenticated;
