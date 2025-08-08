-- Fix Supabase function to handle tiles as array and error gracefully
create or replace function public.process_game_move(
    p_session_id uuid,
    p_tile_index integer
)
returns jsonb
language plpgsql security definer
as $$
declare
    v_game_session record;
    v_tiles jsonb;
    v_new_winnings numeric;
    v_tile jsonb;
    v_revealed_count integer;
    v_multiplier numeric;
begin
    -- Get game session
    select * into v_game_session
    from public.game_sessions
    where id = p_session_id and user_id = auth.uid()
    for update;

    if not found then
        return jsonb_build_object('success', false, 'error', 'Invalid game session');
    end if;

    if v_game_session.state != 'playing' then
        return jsonb_build_object('success', false, 'error', 'Game is not in playing state');
    end if;

    v_tiles := v_game_session.tiles;

    -- Check if tiles is an array
    if jsonb_typeof(v_tiles) != 'array' then
        return jsonb_build_object('success', false, 'error', 'Tiles is not a valid array');
    end if;

    if p_tile_index < 0 or p_tile_index >= jsonb_array_length(v_tiles) then
        return jsonb_build_object('success', false, 'error', 'Invalid tile index');
    end if;

    v_tile := v_tiles->p_tile_index;
    if (v_tile->>'revealed')::boolean then
        return jsonb_build_object('success', false, 'error', 'Tile already revealed');
    end if;

    -- Update tile
    v_tiles := jsonb_set(
        v_tiles,
        array[p_tile_index::text],
        jsonb_set(v_tile, '{revealed}', 'true'::jsonb)
    );

    -- Calculate new state and winnings
    if (v_tile->>'isBomb')::boolean then
        update public.game_sessions
        set tiles = v_tiles,
            state = 'trapped'
        where id = p_session_id;

        return jsonb_build_object(
            'success', true,
            'isReward', false,
            'state', 'trapped'
        );
    else
        v_revealed_count := (
            select count(*)
            from jsonb_array_elements(v_tiles) t
            where (t->>'revealed')::boolean = true
        );

        -- Calculate multiplier
        v_multiplier := (v_game_session.grid_size - v_revealed_count)::numeric /
                       (v_game_session.grid_size - v_game_session.bomb_count - v_revealed_count)::numeric;
        v_new_winnings := v_game_session.bet_amount * v_multiplier * 0.97; -- 3% house edge

        update public.game_sessions
        set tiles = v_tiles,
            current_winnings = v_new_winnings
        where id = p_session_id;

        return jsonb_build_object(
            'success', true,
            'isReward', true,
            'winnings', v_new_winnings,
            'state', 'playing'
        );
    end if;
end;
$$;
