DROP PROCEDURE IF EXISTS update_kontrak_totals_proc;

DELIMITER //

CREATE PROCEDURE update_kontrak_totals_proc(IN kontrak_id_param INT UNSIGNED)
BEGIN
    DECLARE total_units_count INT DEFAULT 0;
    DECLARE nilai_total_amount DECIMAL(15,2) DEFAULT 0;
    DECLARE jenis_sewa_kontrak VARCHAR(10) DEFAULT 'BULANAN';

    -- Get kontrak jenis_sewa
    SELECT jenis_sewa INTO jenis_sewa_kontrak
    FROM kontrak
    WHERE id = kontrak_id_param;

    -- Set default jika NULL
    IF jenis_sewa_kontrak IS NULL THEN
        SET jenis_sewa_kontrak = 'BULANAN';
    END IF;

    -- Calculate totals dari kontrak_spesifikasi
    IF jenis_sewa_kontrak = 'HARIAN' THEN
        SELECT
            COALESCE(SUM(ks.jumlah_dibutuhkan), 0) as total_units,
            COALESCE(SUM(ks.jumlah_dibutuhkan * COALESCE(ks.harga_per_unit_harian, 0)), 0) as nilai_total
            INTO total_units_count, nilai_total_amount
        FROM kontrak_spesifikasi ks
        WHERE ks.kontrak_id = kontrak_id_param;
    ELSE
        -- Default BULANAN
        SELECT
            COALESCE(SUM(ks.jumlah_dibutuhkan), 0) as total_units,
            COALESCE(SUM(ks.jumlah_dibutuhkan * COALESCE(ks.harga_per_unit_bulanan, 0)), 0) as nilai_total
            INTO total_units_count, nilai_total_amount
        FROM kontrak_spesifikasi ks
        WHERE ks.kontrak_id = kontrak_id_param;
    END IF;

    -- Update kontrak
    UPDATE kontrak SET
        total_units = total_units_count,
        nilai_total = nilai_total_amount
    WHERE id = kontrak_id_param;

END //

DELIMITER ;
