-- Create withdrawals table
CREATE TABLE IF NOT EXISTS public.withdrawals (
    id UUID DEFAULT uuid_generate_v4() PRIMARY KEY,
    user_id UUID NOT NULL REFERENCES auth.users(id),
    amount DECIMAL(10,2) NOT NULL CHECK (amount >= 500), -- Minimum withdrawal ₹500
    upi_id TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT 'pending' CHECK (status IN ('pending', 'approved', 'rejected')),
    created_at TIMESTAMPTZ DEFAULT NOW(),
    processed_at TIMESTAMPTZ,
    processed_by UUID REFERENCES auth.users(id),
    notes TEXT,
    CONSTRAINT withdrawal_amount_positive CHECK (amount > 0)
);

-- Add indexes for performance
CREATE INDEX IF NOT EXISTS withdrawals_user_id_idx ON public.withdrawals(user_id);
CREATE INDEX IF NOT EXISTS withdrawals_status_idx ON public.withdrawals(status);

-- Secure the table
ALTER TABLE public.withdrawals ENABLE ROW LEVEL SECURITY;

-- Users can see their own withdrawals
CREATE POLICY "Users can view their own withdrawals"
    ON public.withdrawals
    FOR SELECT
    USING (auth.uid() = user_id);

-- Users can create withdrawal requests
CREATE POLICY "Users can create withdrawal requests"
    ON public.withdrawals
    FOR INSERT
    WITH CHECK (
        auth.uid() = user_id 
        AND status = 'pending'
        AND processed_by IS NULL 
        AND processed_at IS NULL
    );

-- Only admins can update withdrawal status
CREATE POLICY "Admins can update withdrawals"
    ON public.withdrawals
    FOR UPDATE
    USING (
        EXISTS (
            SELECT 1 FROM public.admin_users 
            WHERE user_id = auth.uid()
        )
    );

-- Function to request a withdrawal
CREATE OR REPLACE FUNCTION public.request_withdrawal(
    p_amount DECIMAL,
    p_upi_id TEXT
)
RETURNS JSONB
LANGUAGE plpgsql
SECURITY DEFINER
AS $$
DECLARE
    v_user_id UUID;
    v_current_balance DECIMAL;
    v_pending_withdrawals DECIMAL;
    v_available_balance DECIMAL;
BEGIN
    -- Get current user ID
    v_user_id := auth.uid();
    IF v_user_id IS NULL THEN
        RETURN jsonb_build_object('success', false, 'error', 'User not authenticated');
    END IF;

    -- Validate minimum withdrawal amount
    IF p_amount < 500 THEN
        RETURN jsonb_build_object('success', false, 'error', 'Minimum withdrawal amount is ₹500');
    END IF;

    -- Get current balance
    SELECT balance INTO v_current_balance
    FROM public.users_balance
    WHERE user_id = v_user_id;

    -- Get sum of pending withdrawals
    SELECT COALESCE(SUM(amount), 0) INTO v_pending_withdrawals
    FROM public.withdrawals
    WHERE user_id = v_user_id AND status = 'pending';

    -- Calculate available balance
    v_available_balance := v_current_balance - v_pending_withdrawals;

    -- Check if user has sufficient balance
    IF v_available_balance < p_amount THEN
        RETURN jsonb_build_object('success', false, 'error', 'Insufficient balance');
    END IF;

    -- Create withdrawal request
    INSERT INTO public.withdrawals (user_id, amount, upi_id)
    VALUES (v_user_id, p_amount, p_upi_id);

    RETURN jsonb_build_object('success', true);
EXCEPTION WHEN OTHERS THEN
    RETURN jsonb_build_object('success', false, 'error', SQLERRM);
END;
$$;

-- Function to process a withdrawal (admin only)
CREATE OR REPLACE FUNCTION public.process_withdrawal(
    p_withdrawal_id UUID,
    p_status TEXT,
    p_notes TEXT DEFAULT NULL
)
RETURNS JSONB
LANGUAGE plpgsql
SECURITY DEFINER
AS $$
DECLARE
    v_admin_id UUID;
    v_withdrawal RECORD;
BEGIN
    -- Get current admin ID
    v_admin_id := auth.uid();
    
    -- Verify admin status
    IF NOT EXISTS (
        SELECT 1 FROM public.admin_users 
        WHERE user_id = v_admin_id
    ) THEN
        RETURN jsonb_build_object('success', false, 'error', 'Unauthorized');
    END IF;

    -- Get withdrawal request
    SELECT * INTO v_withdrawal
    FROM public.withdrawals
    WHERE id = p_withdrawal_id AND status = 'pending';

    IF NOT FOUND THEN
        RETURN jsonb_build_object('success', false, 'error', 'Withdrawal request not found or already processed');
    END IF;

    -- Update withdrawal status
    UPDATE public.withdrawals
    SET status = p_status,
        processed_at = NOW(),
        processed_by = v_admin_id,
        notes = p_notes
    WHERE id = p_withdrawal_id;

    -- If approved, deduct from user's balance
    IF p_status = 'approved' THEN
        UPDATE public.users_balance
        SET balance = balance - v_withdrawal.amount
        WHERE user_id = v_withdrawal.user_id;
    END IF;

    RETURN jsonb_build_object('success', true);
EXCEPTION WHEN OTHERS THEN
    RETURN jsonb_build_object('success', false, 'error', SQLERRM);
END;
$$;

-- Function to get user's withdrawal history
CREATE OR REPLACE FUNCTION public.get_withdrawal_history()
RETURNS TABLE (
    id UUID,
    amount DECIMAL,
    upi_id TEXT,
    status TEXT,
    created_at TIMESTAMPTZ,
    processed_at TIMESTAMPTZ
)
LANGUAGE plpgsql
SECURITY DEFINER
AS $$
BEGIN
    RETURN QUERY
    SELECT 
        w.id,
        w.amount,
        w.upi_id,
        w.status,
        w.created_at,
        w.processed_at
    FROM public.withdrawals w
    WHERE w.user_id = auth.uid()
    ORDER BY w.created_at DESC;
END;
$$;

-- Add withdrawal_count to users_balance view
DROP VIEW IF EXISTS public.users_balance_view;
CREATE VIEW public.users_balance_view AS
SELECT 
    ub.user_id,
    u.email,
    u.raw_user_meta_data->>'full_name' as full_name,
    ub.balance,
    COUNT(DISTINCT w.id) as withdrawal_count,
    SUM(CASE WHEN w.status = 'approved' THEN w.amount ELSE 0 END) as total_withdrawn
FROM public.users_balance ub
LEFT JOIN auth.users u ON u.id = ub.user_id
LEFT JOIN public.withdrawals w ON w.user_id = ub.user_id
GROUP BY ub.user_id, u.email, u.raw_user_meta_data->>'full_name', ub.balance;

-- Grant necessary permissions
GRANT ALL ON public.withdrawals TO authenticated;
GRANT ALL ON public.withdrawals TO service_role;
GRANT EXECUTE ON FUNCTION public.request_withdrawal TO authenticated;
GRANT EXECUTE ON FUNCTION public.process_withdrawal TO authenticated;
GRANT EXECUTE ON FUNCTION public.get_withdrawal_history TO authenticated;
