-- OPTIMA ERP - MERGED MARKETING & ACCOUNTING DATA IMPORT
-- Generated: 2026-02-18 21:15:24
-- Total units to import: 2178

SET FOREIGN_KEY_CHECKS=0;
START TRANSACTION;

-- ========================================
-- STEP 1: RESET OVERLAPPING UNITS
-- Total: 551 units
-- Reason: CSV/Accounting data is SOURCE OF TRUTH
-- ========================================

-- Reset batch 1
UPDATE inventory_unit
SET customer_id = NULL,
    kontrak_id = NULL,
    customer_location_id = NULL,
    area_id = NULL,
    harga_sewa_bulanan = NULL,
    harga_sewa_harian = NULL,
    on_hire_date = NULL,
    off_hire_date = NULL,
    rate_changed_at = NULL
WHERE no_unit IN (2062, 2150, 2155, 2162, 2166, 2167, 2176, 2180, 2232, 2262, 2280, 2282, 2294, 2317, 2342, 5767, 2461, 2466, 2542, 2547, 2549, 2550, 2564, 540, 2611, 2612, 2613, 2614, 2621, 2639, 2651, 2657, 2686, 2694, 2696, 2721, 2754, 2766, 2805, 2827, 2828, 2881, 2889, 2890, 2891, 2892, 2902, 2904, 2952, 5012, 2968, 2969, 2970, 2971, 5016, 5017, 2986, 5034, 5041, 2995, 2996, 2997, 2999, 5048, 3002, 3003, 3004, 3005, 3009, 3010, 5062, 3020, 5082, 5085, 3043, 3044, 3045, 3046, 3047, 3048, 3049, 3050, 1003, 3051, 3057, 3059, 3061, 3063, 3064, 3065, 3066, 3067, 3069, 5118, 5119, 3072, 3073, 3074, 3075, 3076);

-- Reset batch 2
UPDATE inventory_unit
SET customer_id = NULL,
    kontrak_id = NULL,
    customer_location_id = NULL,
    area_id = NULL,
    harga_sewa_bulanan = NULL,
    harga_sewa_harian = NULL,
    on_hire_date = NULL,
    off_hire_date = NULL,
    rate_changed_at = NULL
WHERE no_unit IN (3077, 3078, 3079, 3080, 3081, 3082, 3083, 5124, 5126, 5127, 5128, 5130, 5134, 5136, 5137, 5139, 5140, 5141, 3098, 1052, 5148, 5153, 3108, 5157, 5158, 5159, 3112, 3113, 3114, 3115, 3116, 5164, 3118, 5161, 5162, 3121, 3123, 3133, 3138, 5186, 3140, 3141, 5187, 5190, 3144, 3145, 5192, 3147, 5196, 5198, 3070, 5209, 3162, 5210, 3071, 3165, 5213, 5220, 3176, 5224, 3179, 3180, 3181, 3193, 5244, 5245, 3212, 3213, 5260, 3215, 3216, 3218, 5272, 5274, 3229, 3230, 5279, 5280, 5120, 5281, 5282, 5283, 5284, 5121, 5286, 5288, 5289, 5122, 5293, 5295, 5296, 5123, 1202, 5300, 3252, 3255, 3256, 3257, 5125, 3258);

-- Reset batch 3
UPDATE inventory_unit
SET customer_id = NULL,
    kontrak_id = NULL,
    customer_location_id = NULL,
    area_id = NULL,
    harga_sewa_bulanan = NULL,
    harga_sewa_harian = NULL,
    on_hire_date = NULL,
    off_hire_date = NULL,
    rate_changed_at = NULL
WHERE no_unit IN (3259, 3260, 3261, 3263, 5305, 5306, 5309, 5314, 5321, 5322, 5325, 5129, 5327, 5330, 3288, 5341, 5342, 5343, 3295, 5345, 5346, 5347, 3299, 5348, 5349, 5351, 3304, 5352, 5354, 5355, 5362, 5363, 5364, 5365, 5366, 3319, 3320, 3321, 5370, 3322, 5367, 3325, 3326, 5368, 5369, 5371, 5373, 3331, 5380, 5381, 5383, 5386, 5391, 5393, 5394, 5401, 5408, 3369, 3373, 3374, 3375, 3383, 3384, 5433, 5436, 3390, 3393, 3398, 3400, 3401, 3404, 3405, 3410, 5461, 3413, 5463, 5464, 3414, 3415, 3416, 3417, 3418, 5501, 5502, 1428, 1430, 5529, 5530, 3485, 3491, 5539, 5579, 3502, 3503, 3504, 3505, 3506, 3507, 3508, 3509);

-- Reset batch 4
UPDATE inventory_unit
SET customer_id = NULL,
    kontrak_id = NULL,
    customer_location_id = NULL,
    area_id = NULL,
    harga_sewa_bulanan = NULL,
    harga_sewa_harian = NULL,
    on_hire_date = NULL,
    off_hire_date = NULL,
    rate_changed_at = NULL
WHERE no_unit IN (3510, 3511, 3512, 3513, 3514, 5563, 5564, 5565, 5566, 3515, 3516, 3517, 5570, 5571, 3518, 3519, 3520, 3521, 3522, 3523, 3524, 3526, 3527, 3528, 3529, 3530, 3531, 5585, 5586, 3532, 3534, 5580, 3543, 3544, 3545, 3546, 3548, 3551, 5599, 3554, 5604, 3558, 5606, 5608, 3561, 5609, 1515, 3563, 5610, 5614, 5611, 5612, 5613, 5617, 5619, 5620, 5615, 5622, 5623, 5624, 3577, 3578, 5625, 5626, 3581, 3582, 5627, 5628, 5629, 5630, 5631, 3588, 3589, 3590, 3591, 5636, 5637, 5643, 3609, 3610, 5657, 3612, 3613, 5664, 3622, 3625, 5673, 3628, 3633, 3634, 5682, 5684, 3645, 3646, 1600, 1601, 3650, 3652, 3653, 3654);

-- Reset batch 5
UPDATE inventory_unit
SET customer_id = NULL,
    kontrak_id = NULL,
    customer_location_id = NULL,
    area_id = NULL,
    harga_sewa_bulanan = NULL,
    harga_sewa_harian = NULL,
    on_hire_date = NULL,
    off_hire_date = NULL,
    rate_changed_at = NULL
WHERE no_unit IN (5703, 1608, 3657, 3658, 3665, 3666, 3667, 3669, 3673, 5722, 5723, 5724, 3679, 3680, 3681, 1634, 3682, 3683, 3684, 3686, 3687, 3688, 5616, 5738, 5739, 3692, 5747, 5748, 5749, 5752, 5753, 3706, 3707, 3709, 3713, 3715, 5763, 5764, 5765, 5766, 5768, 5769, 5770, 5771, 5772, 5773, 5774, 5775, 5776, 3731, 5779, 5780, 3734, 3745, 3754, 5808, 5809, 3763, 3764, 3765, 3766, 3767, 3768, 3769, 3771, 3772, 3777, 1736, 3793, 3794, 3795, 3796, 3798, 3801, 3802, 3803, 3804, 1773, 1777, 3830, 3832, 3833, 3834, 3835, 3836, 3838, 3841, 3843, 3846, 3848, 3849, 3851, 3852, 3853, 3854, 3855, 3856, 3860, 3861, 3862);

-- Reset batch 6
UPDATE inventory_unit
SET customer_id = NULL,
    kontrak_id = NULL,
    customer_location_id = NULL,
    area_id = NULL,
    harga_sewa_bulanan = NULL,
    harga_sewa_harian = NULL,
    on_hire_date = NULL,
    off_hire_date = NULL,
    rate_changed_at = NULL
WHERE no_unit IN (3863, 1816, 3864, 3867, 3878, 3902, 3906, 3912, 3913, 3914, 3915, 3916, 3917, 3918, 3919, 3920, 3921, 3924, 3927, 3930, 3931, 1885, 3934, 5985, 5994, 5995, 6000, 6002, 3955, 6007, 6009, 6014, 6015, 6018, 3981, 6029, 6033, 3986, 3988, 3989, 3990, 3991, 3992, 3993, 3995, 6044, 6070, 6072, 1977, 1979, 2018);

-- ========================================
-- STEP 2: INSERT NEW CUSTOMER LOCATIONS
-- Total: 202 locations
-- ========================================

-- Location 1: STEP (Cikarang )
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (228, NULL, 'STEP (Cikarang )', 'LOC-228-1', 'Auto-generated from merge', 'STEP (Cikarang )', 'JABABEKA', 0, 1);
SET @location_1 = LAST_INSERT_ID();

-- Location 2: SURABAYA
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (24, NULL, 'SURABAYA', 'LOC-24-2', 'Auto-generated from merge', 'SURABAYA', 'SIDOARJO', 0, 1);
SET @location_2 = LAST_INSERT_ID();

-- Location 3: Hyundai
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (86, NULL, 'Hyundai', 'LOC-86-3', 'Auto-generated from merge', 'Hyundai', 'HYUNDAI', 0, 1);
SET @location_3 = LAST_INSERT_ID();

-- Location 4: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (46, NULL, 'DEFAULT LOCATION', 'LOC-46-4', 'Auto-generated from merge', 'N/A', 'PURWAKARTA', 0, 1);
SET @location_4 = LAST_INSERT_ID();

-- Location 5: Semarang
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (92, 35, 'Semarang', 'LOC-92-5', 'Auto-generated from merge', 'Semarang', 'SEMARANG', 0, 1);
SET @location_5 = LAST_INSERT_ID();

-- Location 6: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (64, NULL, 'DEFAULT LOCATION', 'LOC-64-6', 'Auto-generated from merge', 'N/A', 'CIKANDE', 0, 1);
SET @location_6 = LAST_INSERT_ID();

-- Location 7: BANDUNG
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (24, NULL, 'BANDUNG', 'LOC-24-7', 'Auto-generated from merge', 'BANDUNG', 'BANDUNG', 0, 1);
SET @location_7 = LAST_INSERT_ID();

-- Location 8: Majalengka
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (55, 26, 'Majalengka', 'LOC-55-8', 'Auto-generated from merge', 'Majalengka', 'MAJALENGKA', 0, 1);
SET @location_8 = LAST_INSERT_ID();

-- Location 9: Bandung
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (1, NULL, 'Bandung', 'LOC-1-9', 'Auto-generated from merge', 'Bandung', 'BANDUNG', 0, 1);
SET @location_9 = LAST_INSERT_ID();

-- Location 10: Karawang
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (92, 19, 'Karawang', 'LOC-92-10', 'Auto-generated from merge', 'Karawang', 'KARAWANG', 0, 1);
SET @location_10 = LAST_INSERT_ID();

-- Location 11: Karawang
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (56, 19, 'Karawang', 'LOC-56-11', 'Auto-generated from merge', 'Karawang', 'KARAWANG', 0, 1);
SET @location_11 = LAST_INSERT_ID();

-- Location 12: GIIC
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (11, NULL, 'GIIC', 'LOC-11-12', 'Auto-generated from merge', 'GIIC', 'HYUNDAI', 0, 1);
SET @location_12 = LAST_INSERT_ID();

-- Location 13: Wuxi Xin Yuan Chuan Construction Engineering-Karawang
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (230, 19, 'Wuxi Xin Yuan Chuan Construction Engineering-Karawang', 'LOC-230-13', 'Auto-generated from merge', 'Wuxi Xin Yuan Chuan Construction Engineering-Karawang', 'KARAWANG', 0, 1);
SET @location_13 = LAST_INSERT_ID();

-- Location 14: Karawang
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (6, 19, 'Karawang', 'LOC-6-14', 'Auto-generated from merge', 'Karawang', 'KARAWANG', 0, 1);
SET @location_14 = LAST_INSERT_ID();

-- Location 15: Bogor
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (13, NULL, 'Bogor', 'LOC-13-15', 'Auto-generated from merge', 'Bogor', 'BOGOR', 0, 1);
SET @location_15 = LAST_INSERT_ID();

-- Location 16: Rancaekek
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (55, NULL, 'Rancaekek', 'LOC-55-16', 'Auto-generated from merge', 'Rancaekek', 'RANCAEKEK', 0, 1);
SET @location_16 = LAST_INSERT_ID();

-- Location 17: Cikampek
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (80, NULL, 'Cikampek', 'LOC-80-17', 'Auto-generated from merge', 'Cikampek', 'CIKAMPEK', 0, 1);
SET @location_17 = LAST_INSERT_ID();

-- Location 18: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (153, NULL, 'DEFAULT LOCATION', 'LOC-153-18', 'Auto-generated from merge', 'N/A', 'NAROGONG', 0, 1);
SET @location_18 = LAST_INSERT_ID();

-- Location 19: BAWEN
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (81, 20, 'BAWEN', 'LOC-81-19', 'Auto-generated from merge', 'BAWEN', 'JATENG', 0, 1);
SET @location_19 = LAST_INSERT_ID();

-- Location 20: Cikarang
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (99, NULL, 'Cikarang', 'LOC-99-20', 'Auto-generated from merge', 'Cikarang', 'JABABEKA', 0, 1);
SET @location_20 = LAST_INSERT_ID();

-- Location 21: ANCOL
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (91, NULL, 'ANCOL', 'LOC-91-21', 'Auto-generated from merge', 'ANCOL', 'JAKARTA', 0, 1);
SET @location_21 = LAST_INSERT_ID();

-- Location 22: T. Priuk
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (229, NULL, 'T. Priuk', 'LOC-229-22', 'Auto-generated from merge', 'T. Priuk', 'JAKARTA', 0, 1);
SET @location_22 = LAST_INSERT_ID();

-- Location 23: Cikampek
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (58, NULL, 'Cikampek', 'LOC-58-23', 'Auto-generated from merge', 'Cikampek', 'BATANG', 0, 1);
SET @location_23 = LAST_INSERT_ID();

-- Location 24: RM
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (95, 17, 'RM', 'LOC-95-24', 'Auto-generated from merge', 'RM', 'SERANG', 0, 1);
SET @location_24 = LAST_INSERT_ID();

-- Location 25: Bogor
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (7, NULL, 'Bogor', 'LOC-7-25', 'Auto-generated from merge', 'Bogor', 'BOGOR', 0, 1);
SET @location_25 = LAST_INSERT_ID();

-- Location 26: TANGERANG
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (21, NULL, 'TANGERANG', 'LOC-21-26', 'Auto-generated from merge', 'TANGERANG', 'TANGGERANG', 0, 1);
SET @location_26 = LAST_INSERT_ID();

-- Location 27: Cikarang
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (35, NULL, 'Cikarang', 'LOC-35-27', 'Auto-generated from merge', 'Cikarang', 'JABABEKA - 2', 0, 1);
SET @location_27 = LAST_INSERT_ID();

-- Location 28: Cikarang, JB-6
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (91, NULL, 'Cikarang, JB-6', 'LOC-91-28', 'Auto-generated from merge', 'Cikarang, JB-6', 'JABABEKA - 2', 0, 1);
SET @location_28 = LAST_INSERT_ID();

-- Location 29: Karawang
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (216, 19, 'Karawang', 'LOC-216-29', 'Auto-generated from merge', 'Karawang', 'KARAWANG', 0, 1);
SET @location_29 = LAST_INSERT_ID();

-- Location 30: unilever medan
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (170, NULL, 'unilever medan', 'LOC-170-30', 'Auto-generated from merge', 'unilever medan', 'MEDAN', 0, 1);
SET @location_30 = LAST_INSERT_ID();

-- Location 31: Purwosari
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (51, NULL, 'Purwosari', 'LOC-51-31', 'Auto-generated from merge', 'Purwosari', 'PURWOSARI', 0, 1);
SET @location_31 = LAST_INSERT_ID();

-- Location 32: Cibitung
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (48, NULL, 'Cibitung', 'LOC-48-32', 'Auto-generated from merge', 'Cibitung', 'BOGOR', 0, 1);
SET @location_32 = LAST_INSERT_ID();

-- Location 33: Bogor
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (78, NULL, 'Bogor', 'LOC-78-33', 'Auto-generated from merge', 'Bogor', 'JAKARTA', 0, 1);
SET @location_33 = LAST_INSERT_ID();

-- Location 34: Subang
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (90, NULL, 'Subang', 'LOC-90-34', 'Auto-generated from merge', 'Subang', 'SUBANG', 0, 1);
SET @location_34 = LAST_INSERT_ID();

-- Location 35: Cirebon
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (65, NULL, 'Cirebon', 'LOC-65-35', 'Auto-generated from merge', 'Cirebon', 'CIREBON', 0, 1);
SET @location_35 = LAST_INSERT_ID();

-- Location 36: Binong pindah per 29 Okt'25 (PMN Sukamulya pagaden)
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (54, NULL, 'Binong pindah per 29 Okt''25 (PMN Sukamulya pagaden)', 'LOC-54-36', 'Auto-generated from merge', 'Binong pindah per 29 Okt''25 (PMN Sukamulya pagaden)', 'CIBINONG', 0, 1);
SET @location_36 = LAST_INSERT_ID();

-- Location 37: Rancaekek
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (101, NULL, 'Rancaekek', 'LOC-101-37', 'Auto-generated from merge', 'Rancaekek', 'DELTAMAS', 0, 1);
SET @location_37 = LAST_INSERT_ID();

-- Location 38: Subang
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (54, NULL, 'Subang', 'LOC-54-38', 'Auto-generated from merge', 'Subang', 'SUBANG', 0, 1);
SET @location_38 = LAST_INSERT_ID();

-- Location 39: Ciracas
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (51, NULL, 'Ciracas', 'LOC-51-39', 'Auto-generated from merge', 'Ciracas', 'CIRACAS', 0, 1);
SET @location_39 = LAST_INSERT_ID();

-- Location 40: Delta Silicon
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (50, NULL, 'Delta Silicon', 'LOC-50-40', 'Auto-generated from merge', 'Delta Silicon', 'HYUNDAI', 0, 1);
SET @location_40 = LAST_INSERT_ID();

-- Location 41: Pandaan
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (51, NULL, 'Pandaan', 'LOC-51-41', 'Auto-generated from merge', 'Pandaan', 'PANDAAN', 0, 1);
SET @location_41 = LAST_INSERT_ID();

-- Location 42: Banjar / Ciamis
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (54, NULL, 'Banjar / Ciamis', 'LOC-54-42', 'Auto-generated from merge', 'Banjar / Ciamis', 'BANJAR', 0, 1);
SET @location_42 = LAST_INSERT_ID();

-- Location 43: DIV. HY
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (195, NULL, 'DIV. HY', 'LOC-195-43', 'Auto-generated from merge', 'DIV. HY', 'BOGOR', 0, 1);
SET @location_43 = LAST_INSERT_ID();

-- Location 44: DIV. WAREHOUSE
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (195, NULL, 'DIV. WAREHOUSE', 'LOC-195-44', 'Auto-generated from merge', 'DIV. WAREHOUSE', 'BOGOR', 0, 1);
SET @location_44 = LAST_INSERT_ID();

-- Location 45: RMPM
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (55, 26, 'RMPM', 'LOC-55-45', 'Auto-generated from merge', 'RMPM', 'MAJALENGKA', 0, 1);
SET @location_45 = LAST_INSERT_ID();

-- Location 46: Majalengka - NUTRIBEV
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (55, 26, 'Majalengka - NUTRIBEV', 'LOC-55-46', 'Auto-generated from merge', 'Majalengka - NUTRIBEV', 'MAJALENGKA', 0, 1);
SET @location_46 = LAST_INSERT_ID();

-- Location 47: Marunda
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (170, 33, 'Marunda', 'LOC-170-47', 'Auto-generated from merge', 'Marunda', 'SURABAYA', 0, 1);
SET @location_47 = LAST_INSERT_ID();

-- Location 48: Ejip
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (100, NULL, 'Ejip', 'LOC-100-48', 'Auto-generated from merge', 'Ejip', 'EJIP', 0, 1);
SET @location_48 = LAST_INSERT_ID();

-- Location 49: SUBANG
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (81, NULL, 'SUBANG', 'LOC-81-49', 'Auto-generated from merge', 'SUBANG', 'SUBANG', 0, 1);
SET @location_49 = LAST_INSERT_ID();

-- Location 50: Cakung
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (49, NULL, 'Cakung', 'LOC-49-50', 'Auto-generated from merge', 'Cakung', 'JAKARTA', 0, 1);
SET @location_50 = LAST_INSERT_ID();

-- Location 51: Pluit
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (95, NULL, 'Pluit', 'LOC-95-51', 'Auto-generated from merge', 'Pluit', 'JAKARTA', 0, 1);
SET @location_51 = LAST_INSERT_ID();

-- Location 52: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (14, NULL, 'DEFAULT LOCATION', 'LOC-14-52', 'Auto-generated from merge', 'N/A', 'CIBITUNG', 0, 1);
SET @location_52 = LAST_INSERT_ID();

-- Location 53: Karawang
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (16, 19, 'Karawang', 'LOC-16-53', 'Auto-generated from merge', 'Karawang', 'KARAWANG', 0, 1);
SET @location_53 = LAST_INSERT_ID();

-- Location 54: CIBITUNG
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (24, NULL, 'CIBITUNG', 'LOC-24-54', 'Auto-generated from merge', 'CIBITUNG', 'CIBITUNG', 0, 1);
SET @location_54 = LAST_INSERT_ID();

-- Location 55: Pandaan
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (96, NULL, 'Pandaan', 'LOC-96-55', 'Auto-generated from merge', 'Pandaan', 'PANDAAN', 0, 1);
SET @location_55 = LAST_INSERT_ID();

-- Location 56: LDC Pasar Rebo
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (4, NULL, 'LDC Pasar Rebo', 'LOC-4-56', 'Auto-generated from merge', 'LDC Pasar Rebo', 'JAKARTA', 0, 1);
SET @location_56 = LAST_INSERT_ID();

-- Location 57: Cikupa
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (54, 13, 'Cikupa', 'LOC-54-57', 'Auto-generated from merge', 'Cikupa', 'CIKUPA', 0, 1);
SET @location_57 = LAST_INSERT_ID();

-- Location 58: KARAWANG
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (45, 17, 'KARAWANG', 'LOC-45-58', 'Auto-generated from merge', 'KARAWANG', 'SERANG', 0, 1);
SET @location_58 = LAST_INSERT_ID();

-- Location 59: DIV. MONAS
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (195, NULL, 'DIV. MONAS', 'LOC-195-59', 'Auto-generated from merge', 'DIV. MONAS', 'BOGOR', 0, 1);
SET @location_59 = LAST_INSERT_ID();

-- Location 60: DIV. BARSOAP
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (195, NULL, 'DIV. BARSOAP', 'LOC-195-60', 'Auto-generated from merge', 'DIV. BARSOAP', 'BOGOR', 0, 1);
SET @location_60 = LAST_INSERT_ID();

-- Location 61: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (214, NULL, 'DEFAULT LOCATION', 'LOC-214-61', 'Auto-generated from merge', 'N/A', 'BOGOR', 0, 1);
SET @location_61 = LAST_INSERT_ID();

-- Location 62: Surabaya
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (92, 33, 'Surabaya', 'LOC-92-62', 'Auto-generated from merge', 'Surabaya', 'SURABAYA', 0, 1);
SET @location_62 = LAST_INSERT_ID();

-- Location 63: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (128, 19, 'DEFAULT LOCATION', 'LOC-128-63', 'Auto-generated from merge', 'N/A', 'KARAWANG', 0, 1);
SET @location_63 = LAST_INSERT_ID();

-- Location 64: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (87, NULL, 'DEFAULT LOCATION', 'LOC-87-64', 'Auto-generated from merge', 'N/A', 'PURWAKARTA', 0, 1);
SET @location_64 = LAST_INSERT_ID();

-- Location 65: MM2100
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (97, NULL, 'MM2100', 'LOC-97-65', 'Auto-generated from merge', 'MM2100', 'CIBITUNG', 0, 1);
SET @location_65 = LAST_INSERT_ID();

-- Location 66: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (114, NULL, 'DEFAULT LOCATION', 'LOC-114-66', 'Auto-generated from merge', 'N/A', 'CIKARANG 2', 0, 1);
SET @location_66 = LAST_INSERT_ID();

-- Location 67: Delta Silicon
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (161, NULL, 'Delta Silicon', 'LOC-161-67', 'Auto-generated from merge', 'Delta Silicon', 'CIKARANG 1', 0, 1);
SET @location_67 = LAST_INSERT_ID();

-- Location 68: Kopo
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (54, NULL, 'Kopo', 'LOC-54-68', 'Auto-generated from merge', 'Kopo', 'KOPO', 0, 1);
SET @location_68 = LAST_INSERT_ID();

-- Location 69: Cicalengka
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (55, NULL, 'Cicalengka', 'LOC-55-69', 'Auto-generated from merge', 'Cicalengka', 'CICALENGKA', 0, 1);
SET @location_69 = LAST_INSERT_ID();

-- Location 70: PT Mecoindo-Ejip
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (227, NULL, 'PT Mecoindo-Ejip', 'LOC-227-70', 'Auto-generated from merge', 'PT Mecoindo-Ejip', 'CIKARANG 2', 0, 1);
SET @location_70 = LAST_INSERT_ID();

-- Location 71: KARAWANG
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (99, NULL, 'KARAWANG', 'LOC-99-71', 'Auto-generated from merge', 'KARAWANG', 'SUBANG', 0, 1);
SET @location_71 = LAST_INSERT_ID();

-- Location 72: Bandung
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (142, NULL, 'Bandung', 'LOC-142-72', 'Auto-generated from merge', 'Bandung', 'BANDUNG', 0, 1);
SET @location_72 = LAST_INSERT_ID();

-- Location 73: Mojokerto
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (225, 21, 'Mojokerto', 'LOC-225-73', 'Auto-generated from merge', 'Mojokerto', 'JATIM', 0, 1);
SET @location_73 = LAST_INSERT_ID();

-- Location 74: JABABEKA
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (53, NULL, 'JABABEKA', 'LOC-53-74', 'Auto-generated from merge', 'JABABEKA', 'JABABEKA - 1', 0, 1);
SET @location_74 = LAST_INSERT_ID();

-- Location 75: Narogong
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (226, NULL, 'Narogong', 'LOC-226-75', 'Auto-generated from merge', 'Narogong', 'NAROGONG', 0, 1);
SET @location_75 = LAST_INSERT_ID();

-- Location 76: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (40, NULL, 'DEFAULT LOCATION', 'LOC-40-76', 'Auto-generated from merge', 'N/A', 'JAKARTA', 0, 1);
SET @location_76 = LAST_INSERT_ID();

-- Location 77: Cianjur
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (54, NULL, 'Cianjur', 'LOC-54-77', 'Auto-generated from merge', 'Cianjur', 'CIANJUR', 0, 1);
SET @location_77 = LAST_INSERT_ID();

-- Location 78: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (13, NULL, 'DEFAULT LOCATION', 'LOC-13-78', 'Auto-generated from merge', 'N/A', 'BOGOR', 0, 1);
SET @location_78 = LAST_INSERT_ID();

-- Location 79: WH HUB (WH DAIWA)
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (64, NULL, 'WH HUB (WH DAIWA)', 'LOC-64-79', 'Auto-generated from merge', 'WH HUB (WH DAIWA)', 'CIKANDE', 0, 1);
SET @location_79 = LAST_INSERT_ID();

-- Location 80: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (88, NULL, 'DEFAULT LOCATION', 'LOC-88-80', 'Auto-generated from merge', 'N/A', 'CIBITUNG', 0, 1);
SET @location_80 = LAST_INSERT_ID();

-- Location 81: Binong pindah per 4 Nov'25 (PMN Sukamulya pagaden) Per 20/11/25 (Pindah ke Pusakanegara Subang)
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (54, NULL, 'Binong pindah per 4 Nov''25 (PMN Sukamulya pagaden) Per 20/11/25 (Pindah ke Pusakanegara Subang)', 'LOC-54-81', 'Auto-generated from merge', 'Binong pindah per 4 Nov''25 (PMN Sukamulya pagaden) Per 20/11/25 (Pindah ke Pusakanegara Subang)', 'CIBINONG', 0, 1);
SET @location_81 = LAST_INSERT_ID();

-- Location 82: Sidoarjo
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (81, NULL, 'Sidoarjo', 'LOC-81-82', 'Auto-generated from merge', 'Sidoarjo', 'SIDOARJO', 0, 1);
SET @location_82 = LAST_INSERT_ID();

-- Location 83: Surabaya
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (4, 33, 'Surabaya', 'LOC-4-83', 'Auto-generated from merge', 'Surabaya', 'SURABAYA', 0, 1);
SET @location_83 = LAST_INSERT_ID();

-- Location 84: Malang
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (4, NULL, 'Malang', 'LOC-4-84', 'Auto-generated from merge', 'Malang', 'MALANG', 0, 1);
SET @location_84 = LAST_INSERT_ID();

-- Location 85: Surakarta
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (74, NULL, 'Surakarta', 'LOC-74-85', 'Auto-generated from merge', 'Surakarta', 'SOLO', 0, 1);
SET @location_85 = LAST_INSERT_ID();

-- Location 86: Bandung
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (4, NULL, 'Bandung', 'LOC-4-86', 'Auto-generated from merge', 'Bandung', 'BANDUNG', 0, 1);
SET @location_86 = LAST_INSERT_ID();

-- Location 87: Pulogadung
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (4, NULL, 'Pulogadung', 'LOC-4-87', 'Auto-generated from merge', 'Pulogadung', 'PULO GADUNG', 0, 1);
SET @location_87 = LAST_INSERT_ID();

-- Location 88: Cikupa
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (4, 13, 'Cikupa', 'LOC-4-88', 'Auto-generated from merge', 'Cikupa', 'CIKUPA', 0, 1);
SET @location_88 = LAST_INSERT_ID();

-- Location 89: Yogyakarta
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (74, NULL, 'Yogyakarta', 'LOC-74-89', 'Auto-generated from merge', 'Yogyakarta', 'YOGYAKARTA', 0, 1);
SET @location_89 = LAST_INSERT_ID();

-- Location 90: Tangerang
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (4, NULL, 'Tangerang', 'LOC-4-90', 'Auto-generated from merge', 'Tangerang', 'TANGGERANG', 0, 1);
SET @location_90 = LAST_INSERT_ID();

-- Location 91: Cikarang
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (4, NULL, 'Cikarang', 'LOC-4-91', 'Auto-generated from merge', 'Cikarang', 'HYUNDAI', 0, 1);
SET @location_91 = LAST_INSERT_ID();

-- Location 92: Bekasi
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (4, 2, 'Bekasi', 'LOC-4-92', 'Auto-generated from merge', 'Bekasi', 'BEKASI', 0, 1);
SET @location_92 = LAST_INSERT_ID();

-- Location 93: Pasar Rebo
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (4, NULL, 'Pasar Rebo', 'LOC-4-93', 'Auto-generated from merge', 'Pasar Rebo', 'PASAR REBO', 0, 1);
SET @location_93 = LAST_INSERT_ID();

-- Location 94: Sawangan
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (4, 16, 'Sawangan', 'LOC-4-94', 'Auto-generated from merge', 'Sawangan', 'SAWANGAN', 0, 1);
SET @location_94 = LAST_INSERT_ID();

-- Location 95: Jakarta
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (42, NULL, 'Jakarta', 'LOC-42-95', 'Auto-generated from merge', 'Jakarta', 'PULO GADUNG', 0, 1);
SET @location_95 = LAST_INSERT_ID();

-- Location 96: GIIC CIKARANG
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (62, NULL, 'GIIC CIKARANG', 'LOC-62-96', 'Auto-generated from merge', 'GIIC CIKARANG', 'DELTAMAS', 0, 1);
SET @location_96 = LAST_INSERT_ID();

-- Location 97: SLEMAN
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (84, NULL, 'SLEMAN', 'LOC-84-97', 'Auto-generated from merge', 'SLEMAN', 'KLATEN', 0, 1);
SET @location_97 = LAST_INSERT_ID();

-- Location 98: KLATEN
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (84, NULL, 'KLATEN', 'LOC-84-98', 'Auto-generated from merge', 'KLATEN', 'KALASAN', 0, 1);
SET @location_98 = LAST_INSERT_ID();

-- Location 99: SIDOARJO
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (81, NULL, 'SIDOARJO', 'LOC-81-99', 'Auto-generated from merge', 'SIDOARJO', 'SIDOARJO', 0, 1);
SET @location_99 = LAST_INSERT_ID();

-- Location 100: Riau
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (58, NULL, 'Riau', 'LOC-58-100', 'Auto-generated from merge', 'Riau', 'BATANG', 0, 1);
SET @location_100 = LAST_INSERT_ID();

-- Location 101: VISCOSE
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (211, NULL, 'VISCOSE', 'LOC-211-101', 'Auto-generated from merge', 'VISCOSE', 'CIKAMPEK', 0, 1);
SET @location_101 = LAST_INSERT_ID();

-- Location 102: JOMBANG
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (199, NULL, 'JOMBANG', 'LOC-199-102', 'Auto-generated from merge', 'JOMBANG', 'JOMBANG', 0, 1);
SET @location_102 = LAST_INSERT_ID();

-- Location 103: Bandung
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (54, NULL, 'Bandung', 'LOC-54-103', 'Auto-generated from merge', 'Bandung', 'GEDEBAGE', 0, 1);
SET @location_103 = LAST_INSERT_ID();

-- Location 104: Bogor
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (47, NULL, 'Bogor', 'LOC-47-104', 'Auto-generated from merge', 'Bogor', 'BOGOR', 0, 1);
SET @location_104 = LAST_INSERT_ID();

-- Location 105: Deltamas
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (32, NULL, 'Deltamas', 'LOC-32-105', 'Auto-generated from merge', 'Deltamas', 'JAKARTA', 0, 1);
SET @location_105 = LAST_INSERT_ID();

-- Location 106: Brebes
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (54, NULL, 'Brebes', 'LOC-54-106', 'Auto-generated from merge', 'Brebes', 'BREBES', 0, 1);
SET @location_106 = LAST_INSERT_ID();

-- Location 107: Cicurug
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (4, NULL, 'Cicurug', 'LOC-4-107', 'Auto-generated from merge', 'Cicurug', 'SUKABUMI', 0, 1);
SET @location_107 = LAST_INSERT_ID();

-- Location 108: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (130, NULL, 'DEFAULT LOCATION', 'LOC-130-108', 'Auto-generated from merge', 'N/A', 'CIKARANG 1', 0, 1);
SET @location_108 = LAST_INSERT_ID();

-- Location 109: Jababeka
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (103, NULL, 'Jababeka', 'LOC-103-109', 'Auto-generated from merge', 'Jababeka', 'JABABEKA', 0, 1);
SET @location_109 = LAST_INSERT_ID();

-- Location 110: (Metalart) KIIC - Karawang
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (68, 19, '(Metalart) KIIC - Karawang', 'LOC-68-110', 'Auto-generated from merge', '(Metalart) KIIC - Karawang', 'KARAWANG', 0, 1);
SET @location_110 = LAST_INSERT_ID();

-- Location 111: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (16, 19, 'DEFAULT LOCATION', 'LOC-16-111', 'Auto-generated from merge', 'N/A', 'KARAWANG', 0, 1);
SET @location_111 = LAST_INSERT_ID();

-- Location 112: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (20, 15, 'DEFAULT LOCATION', 'LOC-20-112', 'Auto-generated from merge', 'N/A', 'JAWILAN', 0, 1);
SET @location_112 = LAST_INSERT_ID();

-- Location 113: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (77, NULL, 'DEFAULT LOCATION', 'LOC-77-113', 'Auto-generated from merge', 'N/A', 'SUBANG', 0, 1);
SET @location_113 = LAST_INSERT_ID();

-- Location 114: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (66, 18, 'DEFAULT LOCATION', 'LOC-66-114', 'Auto-generated from merge', 'N/A', 'TANGERANG', 0, 1);
SET @location_114 = LAST_INSERT_ID();

-- Location 115: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (41, NULL, 'DEFAULT LOCATION', 'LOC-41-115', 'Auto-generated from merge', 'N/A', 'PANDAAN', 0, 1);
SET @location_115 = LAST_INSERT_ID();

-- Location 116: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (92, 19, 'DEFAULT LOCATION', 'LOC-92-116', 'Auto-generated from merge', 'N/A', 'KARAWANG', 0, 1);
SET @location_116 = LAST_INSERT_ID();

-- Location 117: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (39, NULL, 'DEFAULT LOCATION', 'LOC-39-117', 'Auto-generated from merge', 'N/A', 'EJIP', 0, 1);
SET @location_117 = LAST_INSERT_ID();

-- Location 118: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (6, 19, 'DEFAULT LOCATION', 'LOC-6-118', 'Auto-generated from merge', 'N/A', 'KARAWANG', 0, 1);
SET @location_118 = LAST_INSERT_ID();

-- Location 119: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (99, NULL, 'DEFAULT LOCATION', 'LOC-99-119', 'Auto-generated from merge', 'N/A', 'JABABEKA', 0, 1);
SET @location_119 = LAST_INSERT_ID();

-- Location 120: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (177, 21, 'DEFAULT LOCATION', 'LOC-177-120', 'Auto-generated from merge', 'N/A', 'JATIM', 0, 1);
SET @location_120 = LAST_INSERT_ID();

-- Location 121: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (29, NULL, 'DEFAULT LOCATION', 'LOC-29-121', 'Auto-generated from merge', 'N/A', 'EJIP', 0, 1);
SET @location_121 = LAST_INSERT_ID();

-- Location 122: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (51, NULL, 'DEFAULT LOCATION', 'LOC-51-122', 'Auto-generated from merge', 'N/A', 'CIRACAS', 0, 1);
SET @location_122 = LAST_INSERT_ID();

-- Location 123: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (166, NULL, 'DEFAULT LOCATION', 'LOC-166-123', 'Auto-generated from merge', 'N/A', 'JABABEKA', 0, 1);
SET @location_123 = LAST_INSERT_ID();

-- Location 124: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (22, NULL, 'DEFAULT LOCATION', 'LOC-22-124', 'Auto-generated from merge', 'N/A', 'JOGJA', 0, 1);
SET @location_124 = LAST_INSERT_ID();

-- Location 125: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (184, NULL, 'DEFAULT LOCATION', 'LOC-184-125', 'Auto-generated from merge', 'N/A', 'CIBITUNG', 0, 1);
SET @location_125 = LAST_INSERT_ID();

-- Location 126: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (85, NULL, 'DEFAULT LOCATION', 'LOC-85-126', 'Auto-generated from merge', 'N/A', 'BOGOR', 0, 1);
SET @location_126 = LAST_INSERT_ID();

-- Location 127: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (235, 19, 'DEFAULT LOCATION', 'LOC-235-127', 'Auto-generated from merge', 'N/A', 'KARAWANG', 0, 1);
SET @location_127 = LAST_INSERT_ID();

-- Location 128: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (34, NULL, 'DEFAULT LOCATION', 'LOC-34-128', 'Auto-generated from merge', 'N/A', 'CIBITUNG', 0, 1);
SET @location_128 = LAST_INSERT_ID();

-- Location 129: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (98, NULL, 'DEFAULT LOCATION', 'LOC-98-129', 'Auto-generated from merge', 'N/A', 'SUBANG', 0, 1);
SET @location_129 = LAST_INSERT_ID();

-- Location 130: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (220, 33, 'DEFAULT LOCATION', 'LOC-220-130', 'Auto-generated from merge', 'N/A', 'SURABAYA', 0, 1);
SET @location_130 = LAST_INSERT_ID();

-- Location 131: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (45, NULL, 'DEFAULT LOCATION', 'LOC-45-131', 'Auto-generated from merge', 'N/A', 'SERPONG', 0, 1);
SET @location_131 = LAST_INSERT_ID();

-- Location 132: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (224, 27, 'DEFAULT LOCATION', 'LOC-224-132', 'Auto-generated from merge', 'N/A', 'PALEMBANG', 0, 1);
SET @location_132 = LAST_INSERT_ID();

-- Location 133: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (70, NULL, 'DEFAULT LOCATION', 'LOC-70-133', 'Auto-generated from merge', 'N/A', 'EJIP', 0, 1);
SET @location_133 = LAST_INSERT_ID();

-- Location 134: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (199, 19, 'DEFAULT LOCATION', 'LOC-199-134', 'Auto-generated from merge', 'N/A', 'KARAWANG', 0, 1);
SET @location_134 = LAST_INSERT_ID();

-- Location 135: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (58, NULL, 'DEFAULT LOCATION', 'LOC-58-135', 'Auto-generated from merge', 'N/A', 'BATANG', 0, 1);
SET @location_135 = LAST_INSERT_ID();

-- Location 136: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (54, NULL, 'DEFAULT LOCATION', 'LOC-54-136', 'Auto-generated from merge', 'N/A', 'KOPO', 0, 1);
SET @location_136 = LAST_INSERT_ID();

-- Location 137: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (10, 19, 'DEFAULT LOCATION', 'LOC-10-137', 'Auto-generated from merge', 'N/A', 'KARAWANG', 0, 1);
SET @location_137 = LAST_INSERT_ID();

-- Location 138: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (101, NULL, 'DEFAULT LOCATION', 'LOC-101-138', 'Auto-generated from merge', 'N/A', 'DELTAMAS', 0, 1);
SET @location_138 = LAST_INSERT_ID();

-- Location 139: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (15, 19, 'DEFAULT LOCATION', 'LOC-15-139', 'Auto-generated from merge', 'N/A', 'KARAWANG', 0, 1);
SET @location_139 = LAST_INSERT_ID();

-- Location 140: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (8, NULL, 'DEFAULT LOCATION', 'LOC-8-140', 'Auto-generated from merge', 'N/A', 'CIBITUNG', 0, 1);
SET @location_140 = LAST_INSERT_ID();

-- Location 141: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (56, 26, 'DEFAULT LOCATION', 'LOC-56-141', 'Auto-generated from merge', 'N/A', 'MAJALENGKA', 0, 1);
SET @location_141 = LAST_INSERT_ID();

-- Location 142: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (238, NULL, 'DEFAULT LOCATION', 'LOC-238-142', 'Auto-generated from merge', 'N/A', 'CIBITUNG', 0, 1);
SET @location_142 = LAST_INSERT_ID();

-- Location 143: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (190, NULL, 'DEFAULT LOCATION', 'LOC-190-143', 'Auto-generated from merge', 'N/A', 'DELTAMAS', 0, 1);
SET @location_143 = LAST_INSERT_ID();

-- Location 144: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (55, 26, 'DEFAULT LOCATION', 'LOC-55-144', 'Auto-generated from merge', 'N/A', 'MAJALENGKA', 0, 1);
SET @location_144 = LAST_INSERT_ID();

-- Location 145: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (32, NULL, 'DEFAULT LOCATION', 'LOC-32-145', 'Auto-generated from merge', 'N/A', 'JAKARTA', 0, 1);
SET @location_145 = LAST_INSERT_ID();

-- Location 146: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (95, 33, 'DEFAULT LOCATION', 'LOC-95-146', 'Auto-generated from merge', 'N/A', 'SURABAYA', 0, 1);
SET @location_146 = LAST_INSERT_ID();

-- Location 147: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (232, NULL, 'DEFAULT LOCATION', 'LOC-232-147', 'Auto-generated from merge', 'N/A', 'CIKAMPEK', 0, 1);
SET @location_147 = LAST_INSERT_ID();

-- Location 148: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (81, 20, 'DEFAULT LOCATION', 'LOC-81-148', 'Auto-generated from merge', 'N/A', 'JATENG', 0, 1);
SET @location_148 = LAST_INSERT_ID();

-- Location 149: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (132, NULL, 'DEFAULT LOCATION', 'LOC-132-149', 'Auto-generated from merge', 'N/A', 'PADALARANG', 0, 1);
SET @location_149 = LAST_INSERT_ID();

-- Location 150: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (9, NULL, 'DEFAULT LOCATION', 'LOC-9-150', 'Auto-generated from merge', 'N/A', 'JAKARTA', 0, 1);
SET @location_150 = LAST_INSERT_ID();

-- Location 151: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (94, NULL, 'DEFAULT LOCATION', 'LOC-94-151', 'Auto-generated from merge', 'N/A', 'JABABEKA', 0, 1);
SET @location_151 = LAST_INSERT_ID();

-- Location 152: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (219, 34, 'DEFAULT LOCATION', 'LOC-219-152', 'Auto-generated from merge', 'N/A', 'PERAWANG', 0, 1);
SET @location_152 = LAST_INSERT_ID();

-- Location 153: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (7, NULL, 'DEFAULT LOCATION', 'LOC-7-153', 'Auto-generated from merge', 'N/A', 'BOGOR', 0, 1);
SET @location_153 = LAST_INSERT_ID();

-- Location 154: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (150, 35, 'DEFAULT LOCATION', 'LOC-150-154', 'Auto-generated from merge', 'N/A', 'SEMARANG', 0, 1);
SET @location_154 = LAST_INSERT_ID();

-- Location 155: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (143, 18, 'DEFAULT LOCATION', 'LOC-143-155', 'Auto-generated from merge', 'N/A', 'TANGERANG', 0, 1);
SET @location_155 = LAST_INSERT_ID();

-- Location 156: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (24, 21, 'DEFAULT LOCATION', 'LOC-24-156', 'Auto-generated from merge', 'N/A', 'JATIM', 0, 1);
SET @location_156 = LAST_INSERT_ID();

-- Location 157: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (49, NULL, 'DEFAULT LOCATION', 'LOC-49-157', 'Auto-generated from merge', 'N/A', 'CAKUNG', 0, 1);
SET @location_157 = LAST_INSERT_ID();

-- Location 158: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (148, NULL, 'DEFAULT LOCATION', 'LOC-148-158', 'Auto-generated from merge', 'N/A', 'SUKABUMI', 0, 1);
SET @location_158 = LAST_INSERT_ID();

-- Location 159: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (4, NULL, 'DEFAULT LOCATION', 'LOC-4-159', 'Auto-generated from merge', 'N/A', 'SUKABUMI', 0, 1);
SET @location_159 = LAST_INSERT_ID();

-- Location 160: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (218, NULL, 'DEFAULT LOCATION', 'LOC-218-160', 'Auto-generated from merge', 'N/A', 'JAKARTA', 0, 1);
SET @location_160 = LAST_INSERT_ID();

-- Location 161: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (84, NULL, 'DEFAULT LOCATION', 'LOC-84-161', 'Auto-generated from merge', 'N/A', 'BLORA', 0, 1);
SET @location_161 = LAST_INSERT_ID();

-- Location 162: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (2, 20, 'DEFAULT LOCATION', 'LOC-2-162', 'Auto-generated from merge', 'N/A', 'JATENG', 0, 1);
SET @location_162 = LAST_INSERT_ID();

-- Location 163: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (195, NULL, 'DEFAULT LOCATION', 'LOC-195-163', 'Auto-generated from merge', 'N/A', 'BOGOR', 0, 1);
SET @location_163 = LAST_INSERT_ID();

-- Location 164: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (174, NULL, 'DEFAULT LOCATION', 'LOC-174-164', 'Auto-generated from merge', 'N/A', 'JAKARTA', 0, 1);
SET @location_164 = LAST_INSERT_ID();

-- Location 165: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (102, 21, 'DEFAULT LOCATION', 'LOC-102-165', 'Auto-generated from merge', 'N/A', 'JATIM', 0, 1);
SET @location_165 = LAST_INSERT_ID();

-- Location 166: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (126, 27, 'DEFAULT LOCATION', 'LOC-126-166', 'Auto-generated from merge', 'N/A', 'PALEMBANG', 0, 1);
SET @location_166 = LAST_INSERT_ID();

-- Location 167: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (203, NULL, 'DEFAULT LOCATION', 'LOC-203-167', 'Auto-generated from merge', 'N/A', 'DELTAMAS', 0, 1);
SET @location_167 = LAST_INSERT_ID();

-- Location 168: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (237, NULL, 'DEFAULT LOCATION', 'LOC-237-168', 'Auto-generated from merge', 'N/A', 'DELTAMAS', 0, 1);
SET @location_168 = LAST_INSERT_ID();

-- Location 169: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (38, NULL, 'DEFAULT LOCATION', 'LOC-38-169', 'Auto-generated from merge', 'N/A', 'BOGOR', 0, 1);
SET @location_169 = LAST_INSERT_ID();

-- Location 170: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (36, NULL, 'DEFAULT LOCATION', 'LOC-36-170', 'Auto-generated from merge', 'N/A', 'MM2100', 0, 1);
SET @location_170 = LAST_INSERT_ID();

-- Location 171: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (33, NULL, 'DEFAULT LOCATION', 'LOC-33-171', 'Auto-generated from merge', 'N/A', 'DUMAI', 0, 1);
SET @location_171 = LAST_INSERT_ID();

-- Location 172: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (208, NULL, 'DEFAULT LOCATION', 'LOC-208-172', 'Auto-generated from merge', 'N/A', 'TANGGERANG', 0, 1);
SET @location_172 = LAST_INSERT_ID();

-- Location 173: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (91, NULL, 'DEFAULT LOCATION', 'LOC-91-173', 'Auto-generated from merge', 'N/A', 'JABABEKA - 1', 0, 1);
SET @location_173 = LAST_INSERT_ID();

-- Location 174: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (196, NULL, 'DEFAULT LOCATION', 'LOC-196-174', 'Auto-generated from merge', 'N/A', 'BALARAJA', 0, 1);
SET @location_174 = LAST_INSERT_ID();

-- Location 175: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (131, 19, 'DEFAULT LOCATION', 'LOC-131-175', 'Auto-generated from merge', 'N/A', 'KARAWANG', 0, 1);
SET @location_175 = LAST_INSERT_ID();

-- Location 176: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (76, NULL, 'DEFAULT LOCATION', 'LOC-76-176', 'Auto-generated from merge', 'N/A', 'BANDUNG', 0, 1);
SET @location_176 = LAST_INSERT_ID();

-- Location 177: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (233, NULL, 'DEFAULT LOCATION', 'LOC-233-177', 'Auto-generated from merge', 'N/A', 'SUKABUMI', 0, 1);
SET @location_177 = LAST_INSERT_ID();

-- Location 178: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (236, 13, 'DEFAULT LOCATION', 'LOC-236-178', 'Auto-generated from merge', 'N/A', 'CIKUPA', 0, 1);
SET @location_178 = LAST_INSERT_ID();

-- Location 179: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (222, NULL, 'DEFAULT LOCATION', 'LOC-222-179', 'Auto-generated from merge', 'N/A', 'JABABEKA - 1', 0, 1);
SET @location_179 = LAST_INSERT_ID();

-- Location 180: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (118, NULL, 'DEFAULT LOCATION', 'LOC-118-180', 'Auto-generated from merge', 'N/A', 'TANGGERANG', 0, 1);
SET @location_180 = LAST_INSERT_ID();

-- Location 181: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (52, NULL, 'DEFAULT LOCATION', 'LOC-52-181', 'Auto-generated from merge', 'N/A', 'EJIP', 0, 1);
SET @location_181 = LAST_INSERT_ID();

-- Location 182: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (198, 17, 'DEFAULT LOCATION', 'LOC-198-182', 'Auto-generated from merge', 'N/A', 'SERANG', 0, 1);
SET @location_182 = LAST_INSERT_ID();

-- Location 183: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (60, NULL, 'DEFAULT LOCATION', 'LOC-60-183', 'Auto-generated from merge', 'N/A', 'SUKABUMI', 0, 1);
SET @location_183 = LAST_INSERT_ID();

-- Location 184: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (83, 19, 'DEFAULT LOCATION', 'LOC-83-184', 'Auto-generated from merge', 'N/A', 'KARAWANG', 0, 1);
SET @location_184 = LAST_INSERT_ID();

-- Location 185: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (239, 19, 'DEFAULT LOCATION', 'LOC-239-185', 'Auto-generated from merge', 'N/A', 'KARAWANG', 0, 1);
SET @location_185 = LAST_INSERT_ID();

-- Location 186: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (231, 19, 'DEFAULT LOCATION', 'LOC-231-186', 'Auto-generated from merge', 'N/A', 'KARAWANG', 0, 1);
SET @location_186 = LAST_INSERT_ID();

-- Location 187: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (73, NULL, 'DEFAULT LOCATION', 'LOC-73-187', 'Auto-generated from merge', 'N/A', 'DELTAMAS', 0, 1);
SET @location_187 = LAST_INSERT_ID();

-- Location 188: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (230, NULL, 'DEFAULT LOCATION', 'LOC-230-188', 'Auto-generated from merge', 'N/A', 'CIREBON', 0, 1);
SET @location_188 = LAST_INSERT_ID();

-- Location 189: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (207, NULL, 'DEFAULT LOCATION', 'LOC-207-189', 'Auto-generated from merge', 'N/A', 'BOGOR', 0, 1);
SET @location_189 = LAST_INSERT_ID();

-- Location 190: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (142, NULL, 'DEFAULT LOCATION', 'LOC-142-190', 'Auto-generated from merge', 'N/A', 'BANDUNG', 0, 1);
SET @location_190 = LAST_INSERT_ID();

-- Location 191: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (140, NULL, 'DEFAULT LOCATION', 'LOC-140-191', 'Auto-generated from merge', 'N/A', 'EJIP', 0, 1);
SET @location_191 = LAST_INSERT_ID();

-- Location 192: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (47, NULL, 'DEFAULT LOCATION', 'LOC-47-192', 'Auto-generated from merge', 'N/A', 'BOGOR', 0, 1);
SET @location_192 = LAST_INSERT_ID();

-- Location 193: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (129, 19, 'DEFAULT LOCATION', 'LOC-129-193', 'Auto-generated from merge', 'N/A', 'KARAWANG', 0, 1);
SET @location_193 = LAST_INSERT_ID();

-- Location 194: Cibitung
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (19, NULL, 'Cibitung', 'LOC-19-194', 'Auto-generated from accounting data', 'Cibitung', 'N/A', 0, 1);
SET @location_194 = LAST_INSERT_ID();

-- Location 195: Balikpapan
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (197, NULL, 'Balikpapan', 'LOC-197-195', 'Auto-generated from accounting data', 'Balikpapan', 'N/A', 0, 1);
SET @location_195 = LAST_INSERT_ID();

-- Location 196: PALEMBANG
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (3, NULL, 'PALEMBANG', 'LOC-3-196', 'Auto-generated from accounting data', 'PALEMBANG', 'N/A', 0, 1);
SET @location_196 = LAST_INSERT_ID();

-- Location 197: Setu
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (240, NULL, 'Setu', 'LOC-240-197', 'Auto-generated from accounting data', 'Setu', 'N/A', 0, 1);
SET @location_197 = LAST_INSERT_ID();

-- Location 198: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (241, NULL, 'DEFAULT LOCATION', 'LOC-241-198', 'Auto-generated from accounting data', 'DEFAULT LOCATION', 'N/A', 0, 1);
SET @location_198 = LAST_INSERT_ID();

-- Location 199: MUARA ANGKE
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (61, NULL, 'MUARA ANGKE', 'LOC-61-199', 'Auto-generated from accounting data', 'MUARA ANGKE', 'N/A', 0, 1);
SET @location_199 = LAST_INSERT_ID();

-- Location 200: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (158, NULL, 'DEFAULT LOCATION', 'LOC-158-200', 'Auto-generated from accounting data', 'DEFAULT LOCATION', 'N/A', 0, 1);
SET @location_200 = LAST_INSERT_ID();

-- Location 201: Lampung
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (63, NULL, 'Lampung', 'LOC-63-201', 'Auto-generated from accounting data', 'Lampung', 'N/A', 0, 1);
SET @location_201 = LAST_INSERT_ID();

-- Location 202: DEFAULT LOCATION
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (116, NULL, 'DEFAULT LOCATION', 'LOC-116-202', 'Auto-generated from accounting data', 'DEFAULT LOCATION', 'N/A', 0, 1);
SET @location_202 = LAST_INSERT_ID();

-- ========================================
-- STEP 3: INSERT NEW CONTRACTS
-- Total: 532 contracts
-- ========================================

-- Contract 1: PO/BULAN
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (322, 'PO/BULAN', 'PO_ONLY', 'PO/BULAN', '2026-01-21', '2026-02-20', 'ACTIVE', 0);
SET @contract_1 = LAST_INSERT_ID();

-- Contract 2: PO-ID-0006687
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (336, 'PO-ID-0006687', 'PO_ONLY', 'PO-ID-0006687', '2025-04-14', '2028-04-13', 'ACTIVE', 0);
SET @contract_2 = LAST_INSERT_ID();

-- Contract 3: SIK-71113454
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (123, 'SIK-71113454', 'PO_ONLY', 'SIK-71113454', '2025-08-01', '2028-07-31', 'ACTIVE', 0);
SET @contract_3 = LAST_INSERT_ID();

-- Contract 4: GIT/25/020145
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_2, 'GIT/25/020145', 'PO_ONLY', 'GIT/25/020145', '2025-03-23', '2027-03-22', 'ACTIVE', 0);
SET @contract_4 = LAST_INSERT_ID();

-- Contract 5: G400229280
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (70, 'G400229280', 'PO_ONLY', 'G400229280', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_5 = LAST_INSERT_ID();

-- Contract 6: po per bulan
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (275, 'po per bulan', 'PO_ONLY', 'po per bulan', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_6 = LAST_INSERT_ID();

-- Contract 7: SPXID-PO-122390-1
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (216, 'SPXID-PO-122390-1', 'PO_ONLY', 'SPXID-PO-122390-1', '2025-03-01', '2026-02-28', 'ACTIVE', 0);
SET @contract_7 = LAST_INSERT_ID();

-- Contract 8: 063/SML/VIII/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_3, '063/SML/VIII/2025', 'PO_ONLY', '063/SML/VIII/2025', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_8 = LAST_INSERT_ID();

-- Contract 9: 669/SML/XI/2023 Menjadi 800/SML/XII/2024
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (422, '669/SML/XI/2023 Menjadi 800/SML/XII/2024', 'PO_ONLY', '669/SML/XI/2023 Menjadi 800/SML/XII/2024', '2025-01-02', '2026-01-01', 'ACTIVE', 0);
SET @contract_9 = LAST_INSERT_ID();

-- Contract 10: 2300047648
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_4, '2300047648', 'PO_ONLY', '2300047648', '2025-06-01', '2026-05-30', 'ACTIVE', 0);
SET @contract_10 = LAST_INSERT_ID();

-- Contract 11: 4506926481
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (163, '4506926481', 'PO_ONLY', '4506926481', '2025-08-05', '2026-01-04', 'ACTIVE', 0);
SET @contract_11 = LAST_INSERT_ID();

-- Contract 12: 2300047978
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_4, '2300047978', 'PO_ONLY', '2300047978', '2025-06-01', '2026-05-30', 'ACTIVE', 0);
SET @contract_12 = LAST_INSERT_ID();

-- Contract 13: 4506974375
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_5, '4506974375', 'PO_ONLY', '4506974375', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_13 = LAST_INSERT_ID();

-- Contract 14: PO-KPI-17616
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (404, 'PO-KPI-17616', 'PO_ONLY', 'PO-KPI-17616', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_14 = LAST_INSERT_ID();

-- Contract 15: 642/D-SML/I/2023
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (408, '642/D-SML/I/2023', 'PO_ONLY', '642/D-SML/I/2023', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_15 = LAST_INSERT_ID();

-- Contract 16: 4500004748
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_6, '4500004748', 'PO_ONLY', '4500004748', '2024-08-27', '2025-08-26', 'ACTIVE', 0);
SET @contract_16 = LAST_INSERT_ID();

-- Contract 17: GIT/25/020146
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_2, 'GIT/25/020146', 'PO_ONLY', 'GIT/25/020146', '2025-03-23', '2027-03-22', 'ACTIVE', 0);
SET @contract_17 = LAST_INSERT_ID();

-- Contract 18: GIT/25/080583
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_2, 'GIT/25/080583', 'PO_ONLY', 'GIT/25/080583', '2025-08-02', '2026-08-01', 'ACTIVE', 0);
SET @contract_18 = LAST_INSERT_ID();

-- Contract 19: 148/SML/X/2019 Addendum I
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (49, '148/SML/X/2019 Addendum I', 'PO_ONLY', '148/SML/X/2019 Addendum I', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_19 = LAST_INSERT_ID();

-- Contract 20: Belum Terima PO
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (99, 'Belum Terima PO', 'PO_ONLY', 'Belum Terima PO', '2025-12-13', '2025-12-12', 'ACTIVE', 0);
SET @contract_20 = LAST_INSERT_ID();

-- Contract 21: 365/SML/VIII/2024
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_7, '365/SML/VIII/2024', 'PO_ONLY', '365/SML/VIII/2024', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_21 = LAST_INSERT_ID();

-- Contract 22: GIT/25/080599
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_2, 'GIT/25/080599', 'PO_ONLY', 'GIT/25/080599', '2025-07-09', '2026-07-07', 'ACTIVE', 0);
SET @contract_22 = LAST_INSERT_ID();

-- Contract 23: C240023923
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_8, 'C240023923', 'PO_ONLY', 'C240023923', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_23 = LAST_INSERT_ID();

-- Contract 24: 098/SP/AKD/XI/2025, 098/SML/XI/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_9, '098/SP/AKD/XI/2025, 098/SML/XI/2025', 'PO_ONLY', '098/SP/AKD/XI/2025, 098/SML/XI/2025', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_24 = LAST_INSERT_ID();

-- Contract 25: 432600015
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (345, '432600015', 'PO_ONLY', '432600015', '2025-02-01', '2028-02-28', 'ACTIVE', 0);
SET @contract_25 = LAST_INSERT_ID();

-- Contract 26: 643/SML/VII/2023
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (383, '643/SML/VII/2023', 'PO_ONLY', '643/SML/VII/2023', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_26 = LAST_INSERT_ID();

-- Contract 27: 021/MRA/JKT-LOG-PRO/
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (383, '021/MRA/JKT-LOG-PRO/', 'PO_ONLY', '021/MRA/JKT-LOG-PRO/', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_27 = LAST_INSERT_ID();

-- Contract 28: 4600488982
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (112, '4600488982', 'PO_ONLY', '4600488982', '2025-11-01', '2026-09-30', 'ACTIVE', 0);
SET @contract_28 = LAST_INSERT_ID();

-- Contract 29: 202601-PO-0009
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (356, '202601-PO-0009', 'PO_ONLY', '202601-PO-0009', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_29 = LAST_INSERT_ID();

-- Contract 30: 4506056306
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_10, '4506056306', 'PO_ONLY', '4506056306', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_30 = LAST_INSERT_ID();

-- Contract 31: 711/SML/V/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (404, '711/SML/V/2025', 'PO_ONLY', '711/SML/V/2025', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_31 = LAST_INSERT_ID();

-- Contract 32: 791/SML/XII/2024
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (86, '791/SML/XII/2024', 'PO_ONLY', '791/SML/XII/2024', '2024-12-18', '2027-12-17', 'ACTIVE', 0);
SET @contract_32 = LAST_INSERT_ID();

-- Contract 33: 44929525
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (244, '44929525', 'PO_ONLY', '44929525', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_33 = LAST_INSERT_ID();

-- Contract 34: SPK/0925/023
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (62, 'SPK/0925/023', 'PO_ONLY', 'SPK/0925/023', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_34 = LAST_INSERT_ID();

-- Contract 35: 045/SML-D/VII/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (421, '045/SML-D/VII/2025', 'PO_ONLY', '045/SML-D/VII/2025', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_35 = LAST_INSERT_ID();

-- Contract 36: PO26-00050
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_12, 'PO26-00050', 'PO_ONLY', 'PO26-00050', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_36 = LAST_INSERT_ID();

-- Contract 37: ADA PO
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (352, 'ADA PO', 'PO_ONLY', 'ADA PO', '2026-01-02', '2027-01-01', 'ACTIVE', 0);
SET @contract_37 = LAST_INSERT_ID();

-- Contract 38: 4600488977
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (113, '4600488977', 'PO_ONLY', '4600488977', '2025-11-01', '2026-09-30', 'ACTIVE', 0);
SET @contract_38 = LAST_INSERT_ID();

-- Contract 39: 4600489039
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (113, '4600489039', 'PO_ONLY', '4600489039', '2025-11-01', '2026-09-30', 'ACTIVE', 0);
SET @contract_39 = LAST_INSERT_ID();

-- Contract 40: 4600489038
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (113, '4600489038', 'PO_ONLY', '4600489038', '2025-11-01', '2026-09-30', 'ACTIVE', 0);
SET @contract_40 = LAST_INSERT_ID();

-- Contract 41: IDN10030658
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (98, 'IDN10030658', 'PO_ONLY', 'IDN10030658', '2025-12-05', '2026-11-04', 'ACTIVE', 0);
SET @contract_41 = LAST_INSERT_ID();

-- Contract 42: 4600488988
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (114, '4600488988', 'PO_ONLY', '4600488988', '2025-11-01', '2026-09-30', 'ACTIVE', 0);
SET @contract_42 = LAST_INSERT_ID();

-- Contract 43: 4600488976
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (113, '4600488976', 'PO_ONLY', '4600488976', '2025-11-01', '2026-09-30', 'ACTIVE', 0);
SET @contract_43 = LAST_INSERT_ID();

-- Contract 44: 4600488983
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (112, '4600488983', 'PO_ONLY', '4600488983', '2025-11-01', '2026-09-30', 'ACTIVE', 0);
SET @contract_44 = LAST_INSERT_ID();

-- Contract 45: 4600488987
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (114, '4600488987', 'PO_ONLY', '4600488987', '2025-11-01', '2026-09-30', 'ACTIVE', 0);
SET @contract_45 = LAST_INSERT_ID();

-- Contract 46: 4600488981
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (112, '4600488981', 'PO_ONLY', '4600488981', '2025-11-01', '2026-09-30', 'ACTIVE', 0);
SET @contract_46 = LAST_INSERT_ID();

-- Contract 47: 0006/PO/FUN/I/2026
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (396, '0006/PO/FUN/I/2026', 'PO_ONLY', '0006/PO/FUN/I/2026', '2026-01-27', '2026-01-26', 'ACTIVE', 0);
SET @contract_47 = LAST_INSERT_ID();

-- Contract 48: ada po Mob 2jt
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_13, 'ada po Mob 2jt', 'PO_ONLY', 'ada po Mob 2jt', '2026-01-08', '2026-02-07', 'ACTIVE', 0);
SET @contract_48 = LAST_INSERT_ID();

-- Contract 49: JSH-PO00061618/9/25/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_14, 'JSH-PO00061618/9/25/2025', 'PO_ONLY', 'JSH-PO00061618/9/25/2025', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_49 = LAST_INSERT_ID();

-- Contract 50: 4525005918
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (390, '4525005918', 'PO_ONLY', '4525005918', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_50 = LAST_INSERT_ID();

-- Contract 51: 44/PO/CL/SINO/XII/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (329, '44/PO/CL/SINO/XII/2025', 'PO_ONLY', '44/PO/CL/SINO/XII/2025', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_51 = LAST_INSERT_ID();

-- Contract 52: SPK/0523/020
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (62, 'SPK/0523/020', 'PO_ONLY', 'SPK/0523/020', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_52 = LAST_INSERT_ID();

-- Contract 53: 103/SML/Add.1/XII/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_15, '103/SML/Add.1/XII/2025', 'PO_ONLY', '103/SML/Add.1/XII/2025', '2025-11-15', '2026-11-14', 'ACTIVE', 0);
SET @contract_53 = LAST_INSERT_ID();

-- Contract 54: 4400868810 & 4400868811
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_17, '4400868810 & 4400868811', 'PO_ONLY', '4400868810 & 4400868811', '2023-04-01', '2026-04-01', 'ACTIVE', 0);
SET @contract_54 = LAST_INSERT_ID();

-- Contract 55: PO/001
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_18, 'PO/001', 'PO_ONLY', 'PO/001', '2025-08-22', '2026-02-21', 'ACTIVE', 0);
SET @contract_55 = LAST_INSERT_ID();

-- Contract 56: PO2025090054
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (435, 'PO2025090054', 'PO_ONLY', 'PO2025090054', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_56 = LAST_INSERT_ID();

-- Contract 57: IDN10026785
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (96, 'IDN10026785', 'PO_ONLY', 'IDN10026785', '2025-09-03', '2026-08-02', 'ACTIVE', 0);
SET @contract_57 = LAST_INSERT_ID();

-- Contract 58: 71113301
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_19, '71113301', 'PO_ONLY', '71113301', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_58 = LAST_INSERT_ID();

-- Contract 59: 45/PO/CL/SINO/XII/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (329, '45/PO/CL/SINO/XII/2025', 'PO_ONLY', '45/PO/CL/SINO/XII/2025', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_59 = LAST_INSERT_ID();

-- Contract 60: PO25-00927-1
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_12, 'PO25-00927-1', 'PO_ONLY', 'PO25-00927-1', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_60 = LAST_INSERT_ID();

-- Contract 61: TGR-71110905
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (126, 'TGR-71110905', 'PO_ONLY', 'TGR-71110905', '2023-05-16', '2026-05-15', 'ACTIVE', 0);
SET @contract_61 = LAST_INSERT_ID();

-- Contract 62: 010/KALP/V/2024
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (92, '010/KALP/V/2024', 'PO_ONLY', '010/KALP/V/2024', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_62 = LAST_INSERT_ID();

-- Contract 63: 24007014
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_20, '24007014', 'PO_ONLY', '24007014', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_63 = LAST_INSERT_ID();

-- Contract 64: 037/SML/VI/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (218, '037/SML/VI/2025', 'PO_ONLY', '037/SML/VI/2025', '2025-06-23', '2029-06-22', 'ACTIVE', 0);
SET @contract_64 = LAST_INSERT_ID();

-- Contract 65: 44967667
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_24, '44967667', 'PO_ONLY', '44967667', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_65 = LAST_INSERT_ID();

-- Contract 66: Po Perbulan
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (423, 'Po Perbulan', 'PO_ONLY', 'Po Perbulan', '2026-01-29', '2026-02-28', 'ACTIVE', 0);
SET @contract_66 = LAST_INSERT_ID();

-- Contract 67: CS-200110814
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (84, 'CS-200110814', 'PO_ONLY', 'CS-200110814', '2025-03-19', '2026-03-10', 'ACTIVE', 0);
SET @contract_67 = LAST_INSERT_ID();

-- Contract 68: IDN10029193
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (95, 'IDN10029193', 'PO_ONLY', 'IDN10029193', '2025-11-06', '2026-01-05', 'ACTIVE', 0);
SET @contract_68 = LAST_INSERT_ID();

-- Contract 69: GIT/25/050340
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_7, 'GIT/25/050340', 'PO_ONLY', 'GIT/25/050340', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_69 = LAST_INSERT_ID();

-- Contract 70: 745/SML-RI/IX/2024
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (429, '745/SML-RI/IX/2024', 'PO_ONLY', '745/SML-RI/IX/2024', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_70 = LAST_INSERT_ID();

-- Contract 71: 2311/I/SML/26/P2
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (440, '2311/I/SML/26/P2', 'PO_ONLY', '2311/I/SML/26/P2', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_71 = LAST_INSERT_ID();

-- Contract 72: 1014203
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (433, '1014203', 'PO_ONLY', '1014203', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_72 = LAST_INSERT_ID();

-- Contract 73: PO Perbulan
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (228, 'PO Perbulan', 'PO_ONLY', 'PO Perbulan', '2026-01-01', '2026-12-31', 'ACTIVE', 0);
SET @contract_73 = LAST_INSERT_ID();

-- Contract 74: 4505478461
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_10, '4505478461', 'PO_ONLY', '4505478461', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_74 = LAST_INSERT_ID();

-- Contract 75: 02/PURC.CMBP/12/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_26, '02/PURC.CMBP/12/2025', 'PO_ONLY', '02/PURC.CMBP/12/2025', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_75 = LAST_INSERT_ID();

-- Contract 76: IDL / 75346052 / 1207
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (110, 'IDL / 75346052 / 1207', 'PO_ONLY', 'IDL / 75346052 / 1207', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_76 = LAST_INSERT_ID();

-- Contract 77: TIDAK ADA PO
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (376, 'TIDAK ADA PO', 'PO_ONLY', 'TIDAK ADA PO', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_77 = LAST_INSERT_ID();

-- Contract 78: 133/LGL-0399/PTIL/WHS-C3/VII/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (175, '133/LGL-0399/PTIL/WHS-C3/VII/2025', 'PO_ONLY', '133/LGL-0399/PTIL/WHS-C3/VII/2025', '2021-01-06', '2026-03-31', 'ACTIVE', 0);
SET @contract_78 = LAST_INSERT_ID();

-- Contract 79: SPK/0124/011
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (62, 'SPK/0124/011', 'PO_ONLY', 'SPK/0124/011', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_79 = LAST_INSERT_ID();

-- Contract 80: G400165466
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (68, 'G400165466', 'PO_ONLY', 'G400165466', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_80 = LAST_INSERT_ID();

-- Contract 81: GEDUNG A - DC
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_8, 'GEDUNG A - DC', 'PO_ONLY', 'GEDUNG A - DC', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_81 = LAST_INSERT_ID();

-- Contract 82: S024158
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (62, 'S024158', 'PO_ONLY', 'S024158', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_82 = LAST_INSERT_ID();

-- Contract 83: dalam proses
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (220, 'dalam proses', 'PO_ONLY', 'dalam proses', '2026-01-01', '2026-12-31', 'ACTIVE', 0);
SET @contract_83 = LAST_INSERT_ID();

-- Contract 84: 2300047324
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_4, '2300047324', 'PO_ONLY', '2300047324', '2025-06-01', '2026-05-30', 'ACTIVE', 0);
SET @contract_84 = LAST_INSERT_ID();

-- Contract 85: IDN10026528
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (94, 'IDN10026528', 'PO_ONLY', 'IDN10026528', '2025-08-03', '2026-02-02', 'ACTIVE', 0);
SET @contract_85 = LAST_INSERT_ID();

-- Contract 86: PO00068491 PO LANJUTAN PO00065715
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (240, 'PO00068491 PO LANJUTAN PO00065715', 'PO_ONLY', 'PO00068491 PO LANJUTAN PO00065715', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_86 = LAST_INSERT_ID();

-- Contract 87: 4500007162
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_6, '4500007162', 'PO_ONLY', '4500007162', '2024-12-01', '2025-11-01', 'ACTIVE', 0);
SET @contract_87 = LAST_INSERT_ID();

-- Contract 88: 038/SML/VI/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_29, '038/SML/VI/2025', 'PO_ONLY', '038/SML/VI/2025', '2025-03-24', '2028-03-23', 'ACTIVE', 0);
SET @contract_88 = LAST_INSERT_ID();

-- Contract 89: 4400878745
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_17, '4400878745', 'PO_ONLY', '4400878745', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_89 = LAST_INSERT_ID();

-- Contract 90: SLGI0067/02-24/MA0036
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_30, 'SLGI0067/02-24/MA0036', 'PO_ONLY', 'SLGI0067/02-24/MA0036', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_90 = LAST_INSERT_ID();

-- Contract 91: C240023929
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_8, 'C240023929', 'PO_ONLY', 'C240023929', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_91 = LAST_INSERT_ID();

-- Contract 92: 8080511797
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (122, '8080511797', 'PO_ONLY', '8080511797', '2025-11-25', '2026-03-31', 'ACTIVE', 0);
SET @contract_92 = LAST_INSERT_ID();

-- Contract 93: No. AMD 1 -157/LGL-0385/PTIL/WHS-PWS/VII/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_31, 'No. AMD 1 -157/LGL-0385/PTIL/WHS-PWS/VII/2025', 'PO_ONLY', 'No. AMD 1 -157/LGL-0385/PTIL/WHS-PWS/VII/2025', '2025-08-01', '2026-07-31', 'ACTIVE', 0);
SET @contract_93 = LAST_INSERT_ID();

-- Contract 94: G400189107
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (70, 'G400189107', 'PO_ONLY', 'G400189107', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_94 = LAST_INSERT_ID();

-- Contract 95: 2300047871
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (128, '2300047871', 'PO_ONLY', '2300047871', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_95 = LAST_INSERT_ID();

-- Contract 96: SPK/0825/011
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (62, 'SPK/0825/011', 'PO_ONLY', 'SPK/0825/011', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_96 = LAST_INSERT_ID();

-- Contract 97: SP25JN0840
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (390, 'SP25JN0840', 'PO_ONLY', 'SP25JN0840', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_97 = LAST_INSERT_ID();

-- Contract 98: G400213330
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (69, 'G400213330', 'PO_ONLY', 'G400213330', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_98 = LAST_INSERT_ID();

-- Contract 99: 6190000873
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_32, '6190000873', 'PO_ONLY', '6190000873', '2025-07-01', '2026-06-30', 'ACTIVE', 0);
SET @contract_99 = LAST_INSERT_ID();

-- Contract 100: 059/SENFU-SML/ADD/V/VII/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (328, '059/SENFU-SML/ADD/V/VII/2025', 'PO_ONLY', '059/SENFU-SML/ADD/V/VII/2025', '2025-09-01', '2026-03-01', 'ACTIVE', 0);
SET @contract_100 = LAST_INSERT_ID();

-- Contract 101: TGR-71112864
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (126, 'TGR-71112864', 'PO_ONLY', 'TGR-71112864', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_101 = LAST_INSERT_ID();

-- Contract 102: 4700450479
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_33, '4700450479', 'PO_ONLY', '4700450479', '2026-02-08', '2027-02-07', 'ACTIVE', 0);
SET @contract_102 = LAST_INSERT_ID();

-- Contract 103: PORD-25P1-00006495
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_34, 'PORD-25P1-00006495', 'PO_ONLY', 'PORD-25P1-00006495', '2025-11-13', '2025-11-30', 'ACTIVE', 0);
SET @contract_103 = LAST_INSERT_ID();

-- Contract 104: 067/LCI-SML/VII/2022
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_35, '067/LCI-SML/VII/2022', 'PO_ONLY', '067/LCI-SML/VII/2022', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_104 = LAST_INSERT_ID();

-- Contract 105: 4506977899
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (164, '4506977899', 'PO_ONLY', '4506977899', '2025-10-13', '2026-02-12', 'ACTIVE', 0);
SET @contract_105 = LAST_INSERT_ID();

-- Contract 106: 23102017855
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_37, '23102017855', 'PO_ONLY', '23102017855', '2025-11-12', '2026-02-11', 'ACTIVE', 0);
SET @contract_106 = LAST_INSERT_ID();

-- Contract 107: 4033004598
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (387, '4033004598', 'PO_ONLY', '4033004598', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_107 = LAST_INSERT_ID();

-- Contract 108: 4500017481
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_6, '4500017481', 'PO_ONLY', '4500017481', '2025-11-27', '2026-11-26', 'ACTIVE', 0);
SET @contract_108 = LAST_INSERT_ID();

-- Contract 109: AMD II - 166/LGL-0193/PTIL/WHS-C1/V1/2024
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (174, 'AMD II - 166/LGL-0193/PTIL/WHS-C1/V1/2024', 'PO_ONLY', 'AMD II - 166/LGL-0193/PTIL/WHS-C1/V1/2024', '2023-04-01', '2026-03-31', 'ACTIVE', 0);
SET @contract_109 = LAST_INSERT_ID();

-- Contract 110: 4540076745
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (174, '4540076745', 'PO_ONLY', '4540076745', '2025-09-30', '2026-01-31', 'ACTIVE', 0);
SET @contract_110 = LAST_INSERT_ID();

-- Contract 111: TAC-POLC-2510-00250
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_4, 'TAC-POLC-2510-00250', 'PO_ONLY', 'TAC-POLC-2510-00250', '2025-09-29', '2025-10-13', 'ACTIVE', 0);
SET @contract_111 = LAST_INSERT_ID();

-- Contract 112: 817/SML/I/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (404, '817/SML/I/2025', 'PO_ONLY', '817/SML/I/2025', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_112 = LAST_INSERT_ID();

-- Contract 113: PO-2025/12-0531
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (397, 'PO-2025/12-0531', 'PO_ONLY', 'PO-2025/12-0531', '2025-12-22', '2026-03-22', 'ACTIVE', 0);
SET @contract_113 = LAST_INSERT_ID();

-- Contract 114: PO/2024/01468
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (360, 'PO/2024/01468', 'PO_ONLY', 'PO/2024/01468', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_114 = LAST_INSERT_ID();

-- Contract 115: TIM/2403/049
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (431, 'TIM/2403/049', 'PO_ONLY', 'TIM/2403/049', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_115 = LAST_INSERT_ID();

-- Contract 116: 652/SML/VI/2023
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (416, '652/SML/VI/2023', 'PO_ONLY', '652/SML/VI/2023', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_116 = LAST_INSERT_ID();

-- Contract 117: AMD I - 141/LGL-039/PTIL/WHS-JKT/VII/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_39, 'AMD I - 141/LGL-039/PTIL/WHS-JKT/VII/2025', 'PO_ONLY', 'AMD I - 141/LGL-039/PTIL/WHS-JKT/VII/2025', '2023-07-01', '2026-06-30', 'ACTIVE', 0);
SET @contract_117 = LAST_INSERT_ID();

-- Contract 118: 003/LGL-0004/PTIL/WHS-CKR/III/2023
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_40, '003/LGL-0004/PTIL/WHS-CKR/III/2023', 'PO_ONLY', '003/LGL-0004/PTIL/WHS-CKR/III/2023', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_118 = LAST_INSERT_ID();

-- Contract 119: No. AMD 1 - 181/LGL-0396/PTIL/WHS-PAN/VIII/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_41, 'No. AMD 1 - 181/LGL-0396/PTIL/WHS-PAN/VIII/2025', 'PO_ONLY', 'No. AMD 1 - 181/LGL-0396/PTIL/WHS-PAN/VIII/2025', '2025-08-01', '2026-07-31', 'ACTIVE', 0);
SET @contract_119 = LAST_INSERT_ID();

-- Contract 120: 4212032259
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (20, '4212032259', 'PO_ONLY', '4212032259', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_120 = LAST_INSERT_ID();

-- Contract 121: 4400868812
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_17, '4400868812', 'PO_ONLY', '4400868812', '2024-05-01', '2029-04-30', 'ACTIVE', 0);
SET @contract_121 = LAST_INSERT_ID();

-- Contract 122: PO00060404
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (237, 'PO00060404', 'PO_ONLY', 'PO00060404', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_122 = LAST_INSERT_ID();

-- Contract 123: PO00060397
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (237, 'PO00060397', 'PO_ONLY', 'PO00060397', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_123 = LAST_INSERT_ID();

-- Contract 124: 594/SML/I/2023
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (121, '594/SML/I/2023', 'PO_ONLY', '594/SML/I/2023', '2023-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_124 = LAST_INSERT_ID();

-- Contract 125: 184-OTR-UC0525
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (218, '184-OTR-UC0525', 'PO_ONLY', '184-OTR-UC0525', '2025-05-05', '2027-05-05', 'ACTIVE', 0);
SET @contract_125 = LAST_INSERT_ID();

-- Contract 126: tidak pakai po
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (290, 'tidak pakai po', 'PO_ONLY', 'tidak pakai po', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_126 = LAST_INSERT_ID();

-- Contract 127: PII/2025/I/AGMT/HRGA/07
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (410, 'PII/2025/I/AGMT/HRGA/07', 'PO_ONLY', 'PII/2025/I/AGMT/HRGA/07', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_127 = LAST_INSERT_ID();

-- Contract 128: po/bulan
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_43, 'po/bulan', 'PO_ONLY', 'po/bulan', '2023-01-22', '2026-05-21', 'ACTIVE', 0);
SET @contract_128 = LAST_INSERT_ID();

-- Contract 129: Service Agreement
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_44, 'Service Agreement', 'PO_ONLY', 'Service Agreement', '2023-01-22', '2026-05-21', 'ACTIVE', 0);
SET @contract_129 = LAST_INSERT_ID();

-- Contract 130: C240023970 (Tanpa Operator)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_16, 'C240023970 (Tanpa Operator)', 'PO_ONLY', 'C240023970 (Tanpa Operator)', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_130 = LAST_INSERT_ID();

-- Contract 131: C240022129 (Jun'23 s.d Mei'25) belum ada PO
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_45, 'C240022129 (Jun''23 s.d Mei''25) belum ada PO', 'PO_ONLY', 'C240022129 (Jun''23 s.d Mei''25) belum ada PO', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_131 = LAST_INSERT_ID();

-- Contract 132: C240023583
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_46, 'C240023583', 'PO_ONLY', 'C240023583', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_132 = LAST_INSERT_ID();

-- Contract 133: 450016829
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_6, '450016829', 'PO_ONLY', '450016829', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_133 = LAST_INSERT_ID();

-- Contract 134: 4121146426
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (338, '4121146426', 'PO_ONLY', '4121146426', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_134 = LAST_INSERT_ID();

-- Contract 135: 4032006550
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (388, '4032006550', 'PO_ONLY', '4032006550', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_135 = LAST_INSERT_ID();

-- Contract 136: Belum terima PO
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_8, 'Belum terima PO', 'PO_ONLY', 'Belum terima PO', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_136 = LAST_INSERT_ID();

-- Contract 137: C240018599 Des'25(Jun'23 s.d Mei'25) belum ada PO
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_8, 'C240018599 Des''25(Jun''23 s.d Mei''25) belum ada PO', 'PO_ONLY', 'C240018599 Des''25(Jun''23 s.d Mei''25) belum ada PO', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_137 = LAST_INSERT_ID();

-- Contract 138: 3100015897
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (103, '3100015897', 'PO_ONLY', '3100015897', '2025-03-11', '2026-02-10', 'ACTIVE', 0);
SET @contract_138 = LAST_INSERT_ID();

-- Contract 139: 4504774696-GL1
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (223, '4504774696-GL1', 'PO_ONLY', '4504774696-GL1', '2025-05-19', '2030-05-31', 'ACTIVE', 0);
SET @contract_139 = LAST_INSERT_ID();

-- Contract 140: 4720035011
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (213, '4720035011', 'PO_ONLY', '4720035011', '2025-01-01', '2027-12-31', 'ACTIVE', 0);
SET @contract_140 = LAST_INSERT_ID();

-- Contract 141: 294/PURC.CMBP/9/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_26, '294/PURC.CMBP/9/2025', 'PO_ONLY', '294/PURC.CMBP/9/2025', '2024-10-23', '2025-10-22', 'ACTIVE', 0);
SET @contract_141 = LAST_INSERT_ID();

-- Contract 142: SLSI0006/10-24/MA0075
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (412, 'SLSI0006/10-24/MA0075', 'PO_ONLY', 'SLSI0006/10-24/MA0075', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_142 = LAST_INSERT_ID();

-- Contract 143: GEDUNG P2 - DC
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_11, 'GEDUNG P2 - DC', 'PO_ONLY', 'GEDUNG P2 - DC', '2024-08-26', '2026-08-25', 'ACTIVE', 0);
SET @contract_143 = LAST_INSERT_ID();

-- Contract 144: GEDUNG B9 - DC
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_11, 'GEDUNG B9 - DC', 'PO_ONLY', 'GEDUNG B9 - DC', '2024-08-26', '2026-08-25', 'ACTIVE', 0);
SET @contract_144 = LAST_INSERT_ID();

-- Contract 145: 4506322633
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_10, '4506322633', 'PO_ONLY', '4506322633', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_145 = LAST_INSERT_ID();

-- Contract 146: 4506322611
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_10, '4506322611', 'PO_ONLY', '4506322611', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_146 = LAST_INSERT_ID();

-- Contract 147: C240016353
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (182, 'C240016353', 'PO_ONLY', 'C240016353', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_147 = LAST_INSERT_ID();

-- Contract 148: PO00068742- PO00065714
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (237, 'PO00068742- PO00065714', 'PO_ONLY', 'PO00068742- PO00065714', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_148 = LAST_INSERT_ID();

-- Contract 149: 71114021
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (232, '71114021', 'PO_ONLY', '71114021', '2025-12-01', '2026-12-01', 'PENDING', 0);
SET @contract_149 = LAST_INSERT_ID();

-- Contract 150: 4500011911
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (191, '4500011911', 'PO_ONLY', '4500011911', '2026-02-18', '2026-04-01', 'ACTIVE', 0);
SET @contract_150 = LAST_INSERT_ID();

-- Contract 151: 100/SML/XII/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_48, '100/SML/XII/2025', 'PO_ONLY', '100/SML/XII/2025', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_151 = LAST_INSERT_ID();

-- Contract 152: 24007013
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_20, '24007013', 'PO_ONLY', '24007013', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_152 = LAST_INSERT_ID();

-- Contract 153: 33000014444
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (106, '33000014444', 'PO_ONLY', '33000014444', '2022-10-16', '2025-10-15', 'ACTIVE', 0);
SET @contract_153 = LAST_INSERT_ID();

-- Contract 154: 71113594
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_49, '71113594', 'PO_ONLY', '71113594', '2025-06-01', '2028-05-30', 'ACTIVE', 0);
SET @contract_154 = LAST_INSERT_ID();

-- Contract 155: 021/SML/IV/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (416, '021/SML/IV/2025', 'PO_ONLY', '021/SML/IV/2025', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_155 = LAST_INSERT_ID();

-- Contract 156: PO2025040111
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (435, 'PO2025040111', 'PO_ONLY', 'PO2025040111', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_156 = LAST_INSERT_ID();

-- Contract 157: C240023961 (Tanpa Operator)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_16, 'C240023961 (Tanpa Operator)', 'PO_ONLY', 'C240023961 (Tanpa Operator)', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_157 = LAST_INSERT_ID();

-- Contract 158: IDP / 75346054 / 1207
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (110, 'IDP / 75346054 / 1207', 'PO_ONLY', 'IDP / 75346054 / 1207', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_158 = LAST_INSERT_ID();

-- Contract 159: 52059178
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_51, '52059178', 'PO_ONLY', '52059178', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_159 = LAST_INSERT_ID();

-- Contract 160: 71112754
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_49, '71112754', 'PO_ONLY', '71112754', '2024-11-01', '2027-10-30', 'ACTIVE', 0);
SET @contract_160 = LAST_INSERT_ID();

-- Contract 161: 4121133703
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (338, '4121133703', 'PO_ONLY', '4121133703', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_161 = LAST_INSERT_ID();

-- Contract 162: G400088439
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (68, 'G400088439', 'PO_ONLY', 'G400088439', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_162 = LAST_INSERT_ID();

-- Contract 163: 709/SML/IV/2024
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (87, '709/SML/IV/2024', 'PO_ONLY', '709/SML/IV/2024', '2024-04-29', '2027-04-28', 'ACTIVE', 0);
SET @contract_163 = LAST_INSERT_ID();

-- Contract 164: 4505945518/4506977582
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_10, '4505945518/4506977582', 'PO_ONLY', '4505945518/4506977582', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_164 = LAST_INSERT_ID();

-- Contract 165: 782/SML/X/2024
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (89, '782/SML/X/2024', 'PO_ONLY', '782/SML/X/2024', '2024-10-30', '2027-10-29', 'ACTIVE', 0);
SET @contract_165 = LAST_INSERT_ID();

-- Contract 166: 708/SML/IV/2024
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (88, '708/SML/IV/2024', 'PO_ONLY', '708/SML/IV/2024', '2024-04-29', '2027-04-28', 'ACTIVE', 0);
SET @contract_166 = LAST_INSERT_ID();

-- Contract 167: POTOM0004
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (273, 'POTOM0004', 'PO_ONLY', 'POTOM0004', '2023-02-13', '2026-02-12', 'ACTIVE', 0);
SET @contract_167 = LAST_INSERT_ID();

-- Contract 168: GIT/24/090514
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_2, 'GIT/24/090514', 'PO_ONLY', 'GIT/24/090514', '2024-10-08', '2025-09-07', 'ACTIVE', 0);
SET @contract_168 = LAST_INSERT_ID();

-- Contract 169: 673/SML-D/I/2024
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_53, '673/SML-D/I/2024', 'PO_ONLY', '673/SML-D/I/2024', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_169 = LAST_INSERT_ID();

-- Contract 170: GIT/25/110929
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_54, 'GIT/25/110929', 'PO_ONLY', 'GIT/25/110929', '2025-12-15', '2026-12-14', 'ACTIVE', 0);
SET @contract_170 = LAST_INSERT_ID();

-- Contract 171: IDN10030620
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (100, 'IDN10030620', 'PO_ONLY', 'IDN10030620', '2025-12-02', '2026-11-19', 'ACTIVE', 0);
SET @contract_171 = LAST_INSERT_ID();

-- Contract 172: 4540078005
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (281, '4540078005', 'PO_ONLY', '4540078005', '2025-09-25', '2025-12-25', 'ACTIVE', 0);
SET @contract_172 = LAST_INSERT_ID();

-- Contract 173: 4506747847
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_55, '4506747847', 'PO_ONLY', '4506747847', '2025-03-17', '2026-03-16', 'ACTIVE', 0);
SET @contract_173 = LAST_INSERT_ID();

-- Contract 174: G400033118
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (71, 'G400033118', 'PO_ONLY', 'G400033118', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_174 = LAST_INSERT_ID();

-- Contract 175: 295/PURC.CMBP/9/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_26, '295/PURC.CMBP/9/2025', 'PO_ONLY', '295/PURC.CMBP/9/2025', '2025-08-26', '2026-08-25', 'ACTIVE', 0);
SET @contract_175 = LAST_INSERT_ID();

-- Contract 176: PO/TNA/IX/2025/1230
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_58, 'PO/TNA/IX/2025/1230', 'PO_ONLY', 'PO/TNA/IX/2025/1230', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_176 = LAST_INSERT_ID();

-- Contract 177: 78863
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_59, '78863', 'PO_ONLY', '78863', '2023-01-22', '2026-05-21', 'ACTIVE', 0);
SET @contract_177 = LAST_INSERT_ID();

-- Contract 178: 102/SML/XII/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_61, '102/SML/XII/2025', 'PO_ONLY', '102/SML/XII/2025', '2025-12-15', '2026-12-14', 'ACTIVE', 0);
SET @contract_178 = LAST_INSERT_ID();

-- Contract 179: 051/SML-R/VII/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (424, '051/SML-R/VII/2025', 'PO_ONLY', '051/SML-R/VII/2025', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_179 = LAST_INSERT_ID();

-- Contract 180: TGR-71112854
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (126, 'TGR-71112854', 'PO_ONLY', 'TGR-71112854', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_180 = LAST_INSERT_ID();

-- Contract 181: TGR-71112855
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (126, 'TGR-71112855', 'PO_ONLY', 'TGR-71112855', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_181 = LAST_INSERT_ID();

-- Contract 182: 158/PO/PAG-SML/PSM/II/2023
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (125, '158/PO/PAG-SML/PSM/II/2023', 'PO_ONLY', '158/PO/PAG-SML/PSM/II/2023', '2023-01-02', '2024-01-02', 'PENDING', 0);
SET @contract_182 = LAST_INSERT_ID();

-- Contract 183: C240013188
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_8, 'C240013188', 'PO_ONLY', 'C240013188', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_183 = LAST_INSERT_ID();

-- Contract 184: 751/SML/IX/2024
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (89, '751/SML/IX/2024', 'PO_ONLY', '751/SML/IX/2024', '2024-09-27', '2027-09-26', 'ACTIVE', 0);
SET @contract_184 = LAST_INSERT_ID();

-- Contract 185: 4506052897
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_62, '4506052897', 'PO_ONLY', '4506052897', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_185 = LAST_INSERT_ID();

-- Contract 186: (Per Mar'24 Turun harga Rp 15 jt dari Rp 19 jt)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (378, '(Per Mar''24 Turun harga Rp 15 jt dari Rp 19 jt)', 'PO_ONLY', '(Per Mar''24 Turun harga Rp 15 jt dari Rp 19 jt)', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_186 = LAST_INSERT_ID();

-- Contract 187: 810/SML/I/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (87, '810/SML/I/2025', 'PO_ONLY', '810/SML/I/2025', '2025-01-07', '2028-01-06', 'ACTIVE', 0);
SET @contract_187 = LAST_INSERT_ID();

-- Contract 188: POTOM0013
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (273, 'POTOM0013', 'PO_ONLY', 'POTOM0013', '2025-12-08', '0206-12-07', 'ACTIVE', 0);
SET @contract_188 = LAST_INSERT_ID();

-- Contract 189: 4505664004 perpanjang 4506977609
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_10, '4505664004 perpanjang 4506977609', 'PO_ONLY', '4505664004 perpanjang 4506977609', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_189 = LAST_INSERT_ID();

-- Contract 190: 4505945504 perpanjang 4507137707
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_10, '4505945504 perpanjang 4507137707', 'PO_ONLY', '4505945504 perpanjang 4507137707', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_190 = LAST_INSERT_ID();

-- Contract 191: (Per Okt'24 harga naik + 1,5 jt (pergantian Ban non marking)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (379, '(Per Okt''24 harga naik + 1,5 jt (pergantian Ban non marking)', 'PO_ONLY', '(Per Okt''24 harga naik + 1,5 jt (pergantian Ban non marking)', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_191 = LAST_INSERT_ID();

-- Contract 192: CS #6 (UTARA)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (125, 'CS #6 (UTARA)', 'PO_ONLY', 'CS #6 (UTARA)', '2023-01-06', '2024-01-06', 'PENDING', 0);
SET @contract_192 = LAST_INSERT_ID();

-- Contract 193: USER : BPK ISMAIL
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (125, 'USER : BPK ISMAIL', 'PO_ONLY', 'USER : BPK ISMAIL', '2023-01-02', '2024-01-02', 'PENDING', 0);
SET @contract_193 = LAST_INSERT_ID();

-- Contract 194: 01491/SCI/XII/2024
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (391, '01491/SCI/XII/2024', 'PO_ONLY', '01491/SCI/XII/2024', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_194 = LAST_INSERT_ID();

-- Contract 195: 4720035012
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (213, '4720035012', 'PO_ONLY', '4720035012', '2025-01-01', '2027-12-31', 'ACTIVE', 0);
SET @contract_195 = LAST_INSERT_ID();

-- Contract 196: 00181/SCI/II/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (391, '00181/SCI/II/2025', 'PO_ONLY', '00181/SCI/II/2025', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_196 = LAST_INSERT_ID();

-- Contract 197: No. 056/LGL-0254/PTIL/WHS-C1/V11/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (174, 'No. 056/LGL-0254/PTIL/WHS-C1/V11/2025', 'PO_ONLY', 'No. 056/LGL-0254/PTIL/WHS-C1/V11/2025', '2025-07-01', '2026-06-30', 'ACTIVE', 0);
SET @contract_197 = LAST_INSERT_ID();

-- Contract 198: IDN10030619
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (98, 'IDN10030619', 'PO_ONLY', 'IDN10030619', '2026-01-05', '2026-12-06', 'ACTIVE', 0);
SET @contract_198 = LAST_INSERT_ID();

-- Contract 199: PO00046180
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (240, 'PO00046180', 'PO_ONLY', 'PO00046180', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_199 = LAST_INSERT_ID();

-- Contract 200: 604/SML/III/2023 addendum I
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (92, '604/SML/III/2023 addendum I', 'PO_ONLY', '604/SML/III/2023 addendum I', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_200 = LAST_INSERT_ID();

-- Contract 201: PO2025110245
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (435, 'PO2025110245', 'PO_ONLY', 'PO2025110245', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_201 = LAST_INSERT_ID();

-- Contract 202: C240016176
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_16, 'C240016176', 'PO_ONLY', 'C240016176', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_202 = LAST_INSERT_ID();

-- Contract 203: C240016553
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_8, 'C240016553', 'PO_ONLY', 'C240016553', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_203 = LAST_INSERT_ID();

-- Contract 204: 4540078045
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (281, '4540078045', 'PO_ONLY', '4540078045', '2025-11-13', '2026-02-12', 'ACTIVE', 0);
SET @contract_204 = LAST_INSERT_ID();

-- Contract 205: FS #1 (SELATAN)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (125, 'FS #1 (SELATAN)', 'PO_ONLY', 'FS #1 (SELATAN)', '2023-01-06', '2024-01-06', 'PENDING', 0);
SET @contract_205 = LAST_INSERT_ID();

-- Contract 206: FS #3 (UTARA)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (125, 'FS #3 (UTARA)', 'PO_ONLY', 'FS #3 (UTARA)', '2023-01-06', '2024-01-06', 'PENDING', 0);
SET @contract_206 = LAST_INSERT_ID();

-- Contract 207: 4506534712
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (270, '4506534712', 'PO_ONLY', '4506534712', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_207 = LAST_INSERT_ID();

-- Contract 208: 4800003535
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_67, '4800003535', 'PO_ONLY', '4800003535', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_208 = LAST_INSERT_ID();

-- Contract 209: 4540076451
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_31, '4540076451', 'PO_ONLY', '4540076451', '2025-06-04', '2026-04-30', 'ACTIVE', 0);
SET @contract_209 = LAST_INSERT_ID();

-- Contract 210: C240019898
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_8, 'C240019898', 'PO_ONLY', 'C240019898', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_210 = LAST_INSERT_ID();

-- Contract 211: C240019525
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_8, 'C240019525', 'PO_ONLY', 'C240019525', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_211 = LAST_INSERT_ID();

-- Contract 212: C240023831
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_8, 'C240023831', 'PO_ONLY', 'C240023831', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_212 = LAST_INSERT_ID();

-- Contract 213: C240016031 (PO akan direvisi karena salah nominal)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_8, 'C240016031 (PO akan direvisi karena salah nominal)', 'PO_ONLY', 'C240016031 (PO akan direvisi karena salah nominal)', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_213 = LAST_INSERT_ID();

-- Contract 214: 4530171288
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_8, '4530171288', 'PO_ONLY', '4530171288', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_214 = LAST_INSERT_ID();

-- Contract 215: 4031027828
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (386, '4031027828', 'PO_ONLY', '4031027828', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_215 = LAST_INSERT_ID();

-- Contract 216: C240023070
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_69, 'C240023070', 'PO_ONLY', 'C240023070', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_216 = LAST_INSERT_ID();

-- Contract 217: IDN10025991
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (95, 'IDN10025991', 'PO_ONLY', 'IDN10025991', '2025-08-29', '2025-12-28', 'ACTIVE', 0);
SET @contract_217 = LAST_INSERT_ID();

-- Contract 218: STT (SELATAN)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (125, 'STT (SELATAN)', 'PO_ONLY', 'STT (SELATAN)', '2023-01-06', '2024-01-06', 'PENDING', 0);
SET @contract_218 = LAST_INSERT_ID();

-- Contract 219: CS #1 (SELATAN)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (125, 'CS #1 (SELATAN)', 'PO_ONLY', 'CS #1 (SELATAN)', '2023-01-06', '2024-01-06', 'PENDING', 0);
SET @contract_219 = LAST_INSERT_ID();

-- Contract 220: CS #3 (UTARA)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (125, 'CS #3 (UTARA)', 'PO_ONLY', 'CS #3 (UTARA)', '2023-01-06', '2024-01-06', 'PENDING', 0);
SET @contract_220 = LAST_INSERT_ID();

-- Contract 221: 432600047
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (351, '432600047', 'PO_ONLY', '432600047', '2025-12-18', '2026-02-18', 'ACTIVE', 0);
SET @contract_221 = LAST_INSERT_ID();

-- Contract 222: CARTON BOX (SELATAN)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (125, 'CARTON BOX (SELATAN)', 'PO_ONLY', 'CARTON BOX (SELATAN)', '2023-01-06', '2024-01-06', 'PENDING', 0);
SET @contract_222 = LAST_INSERT_ID();

-- Contract 223: ACE-PO0061617/9/25/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_14, 'ACE-PO0061617/9/25/2025', 'PO_ONLY', 'ACE-PO0061617/9/25/2025', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_223 = LAST_INSERT_ID();

-- Contract 224: 1012313
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (433, '1012313', 'PO_ONLY', '1012313', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_224 = LAST_INSERT_ID();

-- Contract 225: IDN10029192
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (95, 'IDN10029192', 'PO_ONLY', 'IDN10029192', '2025-11-03', '2026-01-05', 'ACTIVE', 0);
SET @contract_225 = LAST_INSERT_ID();

-- Contract 226: 3800594891 (SLI 3)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (334, '3800594891 (SLI 3)', 'PO_ONLY', '3800594891 (SLI 3)', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_226 = LAST_INSERT_ID();

-- Contract 227: 4504774693-GL1
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (223, '4504774693-GL1', 'PO_ONLY', '4504774693-GL1', '2025-05-15', '2030-05-31', 'ACTIVE', 0);
SET @contract_227 = LAST_INSERT_ID();

-- Contract 228: PO PERBULAN
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_72, 'PO PERBULAN', 'PO_ONLY', 'PO PERBULAN', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_228 = LAST_INSERT_ID();

-- Contract 229: 692/SML-R/II/2024
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (428, '692/SML-R/II/2024', 'PO_ONLY', '692/SML-R/II/2024', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_229 = LAST_INSERT_ID();

-- Contract 230: PO00055394
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (241, 'PO00055394', 'PO_ONLY', 'PO00055394', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_230 = LAST_INSERT_ID();

-- Contract 231: 22867/AFD-PUD/I/24
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (357, '22867/AFD-PUD/I/24', 'PO_ONLY', '22867/AFD-PUD/I/24', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_231 = LAST_INSERT_ID();

-- Contract 232: 2711/PB/IJMSIPCI08/2024
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_74, '2711/PB/IJMSIPCI08/2024', 'PO_ONLY', '2711/PB/IJMSIPCI08/2024', '2024-11-02', '2027-11-01', 'ACTIVE', 0);
SET @contract_232 = LAST_INSERT_ID();

-- Contract 233: TGR-71112853
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (126, 'TGR-71112853', 'PO_ONLY', 'TGR-71112853', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_233 = LAST_INSERT_ID();

-- Contract 234: 4121132952
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (338, '4121132952', 'PO_ONLY', '4121132952', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_234 = LAST_INSERT_ID();

-- Contract 235: IDP / 75346057 / 1207
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (110, 'IDP / 75346057 / 1207', 'PO_ONLY', 'IDP / 75346057 / 1207', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_235 = LAST_INSERT_ID();

-- Contract 236: 036/SML/VI/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (89, '036/SML/VI/2025', 'PO_ONLY', '036/SML/VI/2025', '2025-06-24', '2028-06-23', 'ACTIVE', 0);
SET @contract_236 = LAST_INSERT_ID();

-- Contract 237: 2300047096
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (151, '2300047096', 'PO_ONLY', '2300047096', '2025-06-01', '2026-05-30', 'ACTIVE', 0);
SET @contract_237 = LAST_INSERT_ID();

-- Contract 238: 654/SML/X/2023
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (428, '654/SML/X/2023', 'PO_ONLY', '654/SML/X/2023', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_238 = LAST_INSERT_ID();

-- Contract 239: 4506977779 (PIC PAK YAN)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (165, '4506977779 (PIC PAK YAN)', 'PO_ONLY', '4506977779 (PIC PAK YAN)', '2025-12-01', '2026-04-01', 'ACTIVE', 0);
SET @contract_239 = LAST_INSERT_ID();

-- Contract 240: TGR-71112862
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (126, 'TGR-71112862', 'PO_ONLY', 'TGR-71112862', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_240 = LAST_INSERT_ID();

-- Contract 241: 3500700208
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (107, '3500700208', 'PO_ONLY', '3500700208', '2026-01-01', '2026-02-28', 'ACTIVE', 0);
SET @contract_241 = LAST_INSERT_ID();

-- Contract 242: WFT/PROC/AGR/I/2025/001
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_75, 'WFT/PROC/AGR/I/2025/001', 'PO_ONLY', 'WFT/PROC/AGR/I/2025/001', '2025-07-01', '2028-06-30', 'ACTIVE', 0);
SET @contract_242 = LAST_INSERT_ID();

-- Contract 243: 4504774699-GL1
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (225, '4504774699-GL1', 'PO_ONLY', '4504774699-GL1', '2025-06-09', '2030-05-31', 'ACTIVE', 0);
SET @contract_243 = LAST_INSERT_ID();

-- Contract 244: 105/HPP/RM/MG/VII/25
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_76, '105/HPP/RM/MG/VII/25', 'PO_ONLY', '105/HPP/RM/MG/VII/25', '2025-09-01', '2025-09-30', 'ACTIVE', 0);
SET @contract_244 = LAST_INSERT_ID();

-- Contract 245: 007/X/SML/2023
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (413, '007/X/SML/2023', 'PO_ONLY', '007/X/SML/2023', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_245 = LAST_INSERT_ID();

-- Contract 246: TGR-71112861
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (126, 'TGR-71112861', 'PO_ONLY', 'TGR-71112861', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_246 = LAST_INSERT_ID();

-- Contract 247: 71112519
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (405, '71112519', 'PO_ONLY', '71112519', '2024-08-01', '2027-08-30', 'ACTIVE', 0);
SET @contract_247 = LAST_INSERT_ID();

-- Contract 248: POC125004757
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (65, 'POC125004757', 'PO_ONLY', 'POC125004757', '2025-08-04', '2026-01-05', 'ACTIVE', 0);
SET @contract_248 = LAST_INSERT_ID();

-- Contract 249: POC125004666
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (65, 'POC125004666', 'PO_ONLY', 'POC125004666', '2025-08-04', '2026-01-05', 'ACTIVE', 0);
SET @contract_249 = LAST_INSERT_ID();

-- Contract 250: 058/SML/VII/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (438, '058/SML/VII/2025', 'PO_ONLY', '058/SML/VII/2025', '2025-07-25', '2028-07-06', 'ACTIVE', 0);
SET @contract_250 = LAST_INSERT_ID();

-- Contract 251: 688/SML/II/2024
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_78, '688/SML/II/2024', 'PO_ONLY', '688/SML/II/2024', '2024-02-16', '2027-02-15', 'ACTIVE', 0);
SET @contract_251 = LAST_INSERT_ID();

-- Contract 252: TGR-71112858
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (126, 'TGR-71112858', 'PO_ONLY', 'TGR-71112858', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_252 = LAST_INSERT_ID();

-- Contract 253: PO/2025/01486
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (360, 'PO/2025/01486', 'PO_ONLY', 'PO/2025/01486', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_253 = LAST_INSERT_ID();

-- Contract 254: 1013210/079/SML/X/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (433, '1013210/079/SML/X/2025', 'PO_ONLY', '1013210/079/SML/X/2025', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_254 = LAST_INSERT_ID();

-- Contract 255: 041/PO/CL/SINO/XI/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (329, '041/PO/CL/SINO/XI/2025', 'PO_ONLY', '041/PO/CL/SINO/XI/2025', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_255 = LAST_INSERT_ID();

-- Contract 256: 4506056286
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_10, '4506056286', 'PO_ONLY', '4506056286', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_256 = LAST_INSERT_ID();

-- Contract 257: 4500002422
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_79, '4500002422', 'PO_ONLY', '4500002422', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_257 = LAST_INSERT_ID();

-- Contract 258: 156/PO/2023
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (285, '156/PO/2023', 'PO_ONLY', '156/PO/2023', '2023-10-02', '2026-10-01', 'ACTIVE', 0);
SET @contract_258 = LAST_INSERT_ID();

-- Contract 259: GIT/25/121048
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_2, 'GIT/25/121048', 'PO_ONLY', 'GIT/25/121048', '2025-08-02', '2026-08-01', 'ACTIVE', 0);
SET @contract_259 = LAST_INSERT_ID();

-- Contract 260: 4506072659
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (271, '4506072659', 'PO_ONLY', '4506072659', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_260 = LAST_INSERT_ID();

-- Contract 261: 4506049708
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (271, '4506049708', 'PO_ONLY', '4506049708', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_261 = LAST_INSERT_ID();

-- Contract 262: 4506056270
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_10, '4506056270', 'PO_ONLY', '4506056270', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_262 = LAST_INSERT_ID();

-- Contract 263: 4506056165
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_10, '4506056165', 'PO_ONLY', '4506056165', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_263 = LAST_INSERT_ID();

-- Contract 264: 4506056149
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_10, '4506056149', 'PO_ONLY', '4506056149', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_264 = LAST_INSERT_ID();

-- Contract 265: 4506049773
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (271, '4506049773', 'PO_ONLY', '4506049773', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_265 = LAST_INSERT_ID();

-- Contract 266: IDN10029194
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (97, 'IDN10029194', 'PO_ONLY', 'IDN10029194', '2024-05-01', '2026-05-31', 'ACTIVE', 0);
SET @contract_266 = LAST_INSERT_ID();

-- Contract 267: SLSI0006/01-24/MA0001R
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (412, 'SLSI0006/01-24/MA0001R', 'PO_ONLY', 'SLSI0006/01-24/MA0001R', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_267 = LAST_INSERT_ID();

-- Contract 268: 4506310095
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (271, '4506310095', 'PO_ONLY', '4506310095', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_268 = LAST_INSERT_ID();

-- Contract 269: PO2025100102
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (435, 'PO2025100102', 'PO_ONLY', 'PO2025100102', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_269 = LAST_INSERT_ID();

-- Contract 270: TGR-71112851
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (126, 'TGR-71112851', 'PO_ONLY', 'TGR-71112851', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_270 = LAST_INSERT_ID();

-- Contract 271: 4600488986
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (112, '4600488986', 'PO_ONLY', '4600488986', '2025-11-01', '2026-09-30', 'ACTIVE', 0);
SET @contract_271 = LAST_INSERT_ID();

-- Contract 272: 4600488978
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (113, '4600488978', 'PO_ONLY', '4600488978', '2025-11-01', '2026-09-30', 'ACTIVE', 0);
SET @contract_272 = LAST_INSERT_ID();

-- Contract 273: 4600489036
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (114, '4600489036', 'PO_ONLY', '4600489036', '2025-11-01', '2026-09-30', 'ACTIVE', 0);
SET @contract_273 = LAST_INSERT_ID();

-- Contract 274: 4600488979
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (113, '4600488979', 'PO_ONLY', '4600488979', '2025-11-01', '2026-09-30', 'ACTIVE', 0);
SET @contract_274 = LAST_INSERT_ID();

-- Contract 275: 4600488985
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (112, '4600488985', 'PO_ONLY', '4600488985', '2025-11-01', '2026-09-30', 'ACTIVE', 0);
SET @contract_275 = LAST_INSERT_ID();

-- Contract 276: 2300047298
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_4, '2300047298', 'PO_ONLY', '2300047298', '2025-06-01', '2026-05-30', 'ACTIVE', 0);
SET @contract_276 = LAST_INSERT_ID();

-- Contract 277: TGR-71112860
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (126, 'TGR-71112860', 'PO_ONLY', 'TGR-71112860', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_277 = LAST_INSERT_ID();

-- Contract 278: 71113194
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (232, '71113194', 'PO_ONLY', '71113194', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_278 = LAST_INSERT_ID();

-- Contract 279: 4600489035
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (114, '4600489035', 'PO_ONLY', '4600489035', '2025-11-01', '2026-09-30', 'ACTIVE', 0);
SET @contract_279 = LAST_INSERT_ID();

-- Contract 280: 4600488975
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (114, '4600488975', 'PO_ONLY', '4600488975', '2025-11-01', '2026-09-30', 'ACTIVE', 0);
SET @contract_280 = LAST_INSERT_ID();

-- Contract 281: TGR-71112852
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (126, 'TGR-71112852', 'PO_ONLY', 'TGR-71112852', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_281 = LAST_INSERT_ID();

-- Contract 282: 042/PO/CL/SINO/XI/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (329, '042/PO/CL/SINO/XI/2025', 'PO_ONLY', '042/PO/CL/SINO/XI/2025', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_282 = LAST_INSERT_ID();

-- Contract 283: TGR-71112939 (ARIBA)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (126, 'TGR-71112939 (ARIBA)', 'PO_ONLY', 'TGR-71112939 (ARIBA)', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_283 = LAST_INSERT_ID();

-- Contract 284: TGR-71112849
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (126, 'TGR-71112849', 'PO_ONLY', 'TGR-71112849', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_284 = LAST_INSERT_ID();

-- Contract 285: 4504968939 perpanjang 4506963149
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_10, '4504968939 perpanjang 4506963149', 'PO_ONLY', '4504968939 perpanjang 4506963149', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_285 = LAST_INSERT_ID();

-- Contract 286: TGR-71112850
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (126, 'TGR-71112850', 'PO_ONLY', 'TGR-71112850', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_286 = LAST_INSERT_ID();

-- Contract 287: ACE-PO00062480/11/5/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_14, 'ACE-PO00062480/11/5/2025', 'PO_ONLY', 'ACE-PO00062480/11/5/2025', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_287 = LAST_INSERT_ID();

-- Contract 288: PO2026010064
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (435, 'PO2026010064', 'PO_ONLY', 'PO2026010064', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_288 = LAST_INSERT_ID();

-- Contract 289: 4531003793
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_82, '4531003793', 'PO_ONLY', '4531003793', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_289 = LAST_INSERT_ID();

-- Contract 290: 4531003792
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_83, '4531003792', 'PO_ONLY', '4531003792', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_290 = LAST_INSERT_ID();

-- Contract 291: 4531003783
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_84, '4531003783', 'PO_ONLY', '4531003783', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_291 = LAST_INSERT_ID();

-- Contract 292: 4531003791
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_85, '4531003791', 'PO_ONLY', '4531003791', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_292 = LAST_INSERT_ID();

-- Contract 293: 4531003652
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_86, '4531003652', 'PO_ONLY', '4531003652', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_293 = LAST_INSERT_ID();

-- Contract 294: 4531003650
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_87, '4531003650', 'PO_ONLY', '4531003650', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_294 = LAST_INSERT_ID();

-- Contract 295: 4531003648
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_88, '4531003648', 'PO_ONLY', '4531003648', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_295 = LAST_INSERT_ID();

-- Contract 296: IDN10026993
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (96, 'IDN10026993', 'PO_ONLY', 'IDN10026993', '2025-09-03', '2026-02-02', 'ACTIVE', 0);
SET @contract_296 = LAST_INSERT_ID();

-- Contract 297: 4531003653
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_89, '4531003653', 'PO_ONLY', '4531003653', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_297 = LAST_INSERT_ID();

-- Contract 298: 4531003654
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_90, '4531003654', 'PO_ONLY', '4531003654', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_298 = LAST_INSERT_ID();

-- Contract 299: 25300702
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (384, '25300702', 'PO_ONLY', '25300702', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_299 = LAST_INSERT_ID();

-- Contract 300: 4793735445
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (406, '4793735445', 'PO_ONLY', '4793735445', '2026-01-01', '2026-12-31', 'ACTIVE', 0);
SET @contract_300 = LAST_INSERT_ID();

-- Contract 301: C240016032
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_16, 'C240016032', 'PO_ONLY', 'C240016032', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_301 = LAST_INSERT_ID();

-- Contract 302: 4531003668
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_91, '4531003668', 'PO_ONLY', '4531003668', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_302 = LAST_INSERT_ID();

-- Contract 303: 4531003649
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_92, '4531003649', 'PO_ONLY', '4531003649', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_303 = LAST_INSERT_ID();

-- Contract 304: 4531003651
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_93, '4531003651', 'PO_ONLY', '4531003651', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_304 = LAST_INSERT_ID();

-- Contract 305: 4531003780
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_94, '4531003780', 'PO_ONLY', '4531003780', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_305 = LAST_INSERT_ID();

-- Contract 306: 3215005327
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_95, '3215005327', 'PO_ONLY', '3215005327', '2025-11-03', '2026-11-02', 'ACTIVE', 0);
SET @contract_306 = LAST_INSERT_ID();

-- Contract 307: 26300070
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (384, '26300070', 'PO_ONLY', '26300070', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_307 = LAST_INSERT_ID();

-- Contract 308: PO2025100047
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (435, 'PO2025100047', 'PO_ONLY', 'PO2025100047', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_308 = LAST_INSERT_ID();

-- Contract 309: 4201175015IDJ2
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_96, '4201175015IDJ2', 'PO_ONLY', '4201175015IDJ2', '2026-01-01', '2026-12-31', 'ACTIVE', 0);
SET @contract_309 = LAST_INSERT_ID();

-- Contract 310: TGR-71112863
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (126, 'TGR-71112863', 'PO_ONLY', 'TGR-71112863', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_310 = LAST_INSERT_ID();

-- Contract 311: 4720035009
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (213, '4720035009', 'PO_ONLY', '4720035009', '2025-01-01', '2027-12-31', 'ACTIVE', 0);
SET @contract_311 = LAST_INSERT_ID();

-- Contract 312: 71113302
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_19, '71113302', 'PO_ONLY', '71113302', '2024-11-01', '2027-08-31', 'ACTIVE', 0);
SET @contract_312 = LAST_INSERT_ID();

-- Contract 313: 4720035010
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (213, '4720035010', 'PO_ONLY', '4720035010', '2025-01-01', '2027-12-31', 'ACTIVE', 0);
SET @contract_313 = LAST_INSERT_ID();

-- Contract 314: 71113230
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_99, '71113230', 'PO_ONLY', '71113230', '2025-05-01', '2028-04-30', 'ACTIVE', 0);
SET @contract_314 = LAST_INSERT_ID();

-- Contract 315: 8080488987
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (122, '8080488987', 'PO_ONLY', '8080488987', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_315 = LAST_INSERT_ID();

-- Contract 316: 033/SML/V/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (313, '033/SML/V/2025', 'PO_ONLY', '033/SML/V/2025', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_316 = LAST_INSERT_ID();

-- Contract 317: 4506625446 (PIC PAK YANSRI)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (162, '4506625446 (PIC PAK YANSRI)', 'PO_ONLY', '4506625446 (PIC PAK YANSRI)', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_317 = LAST_INSERT_ID();

-- Contract 318: PO00048274
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (237, 'PO00048274', 'PO_ONLY', 'PO00048274', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_318 = LAST_INSERT_ID();

-- Contract 319: 014/SML-YANMAR/I/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_48, '014/SML-YANMAR/I/2025', 'PO_ONLY', '014/SML-YANMAR/I/2025', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_319 = LAST_INSERT_ID();

-- Contract 320: 71112753
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_49, '71112753', 'PO_ONLY', '71112753', '2024-11-01', '2027-10-30', 'ACTIVE', 0);
SET @contract_320 = LAST_INSERT_ID();

-- Contract 321: 018/SML-R/III/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_101, '018/SML-R/III/2025', 'PO_ONLY', '018/SML-R/III/2025', '2025-03-02', '2026-03-01', 'ACTIVE', 0);
SET @contract_321 = LAST_INSERT_ID();

-- Contract 322: 4610021743/21877/PEP-SML/X/2024
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (232, '4610021743/21877/PEP-SML/X/2024', 'PO_ONLY', '4610021743/21877/PEP-SML/X/2024', '2025-12-01', '2026-12-01', 'PENDING', 0);
SET @contract_322 = LAST_INSERT_ID();

-- Contract 323: 71112786
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (232, '71112786', 'PO_ONLY', '71112786', '2025-12-01', '2026-12-01', 'PENDING', 0);
SET @contract_323 = LAST_INSERT_ID();

-- Contract 324: po/bln
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_96, 'po/bln', 'PO_ONLY', 'po/bln', '2025-10-22', '2027-10-21', 'ACTIVE', 0);
SET @contract_324 = LAST_INSERT_ID();

-- Contract 325: 077/SML/IX/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_48, '077/SML/IX/2025', 'PO_ONLY', '077/SML/IX/2025', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_325 = LAST_INSERT_ID();

-- Contract 326: 432500640
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (351, '432500640', 'PO_ONLY', '432500640', '2025-10-22', '2026-10-31', 'ACTIVE', 0);
SET @contract_326 = LAST_INSERT_ID();

-- Contract 327: 017/JASA/IK/2114/X/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_104, '017/JASA/IK/2114/X/2025', 'PO_ONLY', '017/JASA/IK/2114/X/2025', '2025-10-01', '2028-10-31', 'ACTIVE', 0);
SET @contract_327 = LAST_INSERT_ID();

-- Contract 328: 71113303
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_19, '71113303', 'PO_ONLY', '71113303', '2024-11-01', '2027-08-31', 'ACTIVE', 0);
SET @contract_328 = LAST_INSERT_ID();

-- Contract 329: 4506151905
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_5, '4506151905', 'PO_ONLY', '4506151905', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_329 = LAST_INSERT_ID();

-- Contract 330: 4504999419 perpanjang 4506968078
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_10, '4504999419 perpanjang 4506968078', 'PO_ONLY', '4504999419 perpanjang 4506968078', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_330 = LAST_INSERT_ID();

-- Contract 331: 4504999419 perpanjang 4506968061
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_10, '4504999419 perpanjang 4506968061', 'PO_ONLY', '4504999419 perpanjang 4506968061', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_331 = LAST_INSERT_ID();

-- Contract 332: 4504995747 perpanjang 4506965385
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_10, '4504995747 perpanjang 4506965385', 'PO_ONLY', '4504995747 perpanjang 4506965385', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_332 = LAST_INSERT_ID();

-- Contract 333: 716 /SML-R/V/2024
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (166, '716 /SML-R/V/2024', 'PO_ONLY', '716 /SML-R/V/2024', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_333 = LAST_INSERT_ID();

-- Contract 334: GIT/25/00583
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_2, 'GIT/25/00583', 'PO_ONLY', 'GIT/25/00583', '2025-08-02', '2026-08-01', 'ACTIVE', 0);
SET @contract_334 = LAST_INSERT_ID();

-- Contract 335: 043/PO/CL/SINO/XI/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (333, '043/PO/CL/SINO/XI/2025', 'PO_ONLY', '043/PO/CL/SINO/XI/2025', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_335 = LAST_INSERT_ID();

-- Contract 336: 71113941
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (232, '71113941', 'PO_ONLY', '71113941', '2025-12-01', '2026-12-01', 'PENDING', 0);
SET @contract_336 = LAST_INSERT_ID();

-- Contract 337: 71113942
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (232, '71113942', 'PO_ONLY', '71113942', '2025-12-01', '2026-12-01', 'PENDING', 0);
SET @contract_337 = LAST_INSERT_ID();

-- Contract 338: PO00065941
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (239, 'PO00065941', 'PO_ONLY', 'PO00065941', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_338 = LAST_INSERT_ID();

-- Contract 339: CS-200110828
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (84, 'CS-200110828', 'PO_ONLY', 'CS-200110828', '2025-03-22', '2026-03-21', 'ACTIVE', 0);
SET @contract_339 = LAST_INSERT_ID();

-- Contract 340: PO04-251200117
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_109, 'PO04-251200117', 'PO_ONLY', 'PO04-251200117', '2026-01-19', '2027-01-18', 'ACTIVE', 0);
SET @contract_340 = LAST_INSERT_ID();

-- Contract 341: NO.ADD04-001/RTL/SML-KAL/01/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (454, 'NO.ADD04-001/RTL/SML-KAL/01/2025', 'PO_ONLY', 'NO.ADD04-001/RTL/SML-KAL/01/2025', '2025-05-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_341 = LAST_INSERT_ID();

-- Contract 342: ADD II Forklift Agreement
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_114, 'ADD II Forklift Agreement', 'PO_ONLY', 'ADD II Forklift Agreement', '2025-12-01', '2027-11-30', 'ACTIVE', 0);
SET @contract_342 = LAST_INSERT_ID();

-- Contract 343: 107/SML-FORKLIFT/IBR/VI/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_4, '107/SML-FORKLIFT/IBR/VI/2025', 'PO_ONLY', '107/SML-FORKLIFT/IBR/VI/2025', '2025-06-01', '2026-05-30', 'ACTIVE', 0);
SET @contract_343 = LAST_INSERT_ID();

-- Contract 344: 4500105264
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_117, '4500105264', 'PO_ONLY', '4500105264', '2025-08-19', '2025-10-18', 'ACTIVE', 0);
SET @contract_344 = LAST_INSERT_ID();

-- Contract 345: DO.1-3700-1038-16 Add 15
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_117, 'DO.1-3700-1038-16 Add 15', 'PO_ONLY', 'DO.1-3700-1038-16 Add 15', '2024-06-01', '2027-05-31', 'ACTIVE', 0);
SET @contract_345 = LAST_INSERT_ID();

-- Contract 346: 4501607263
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_124, '4501607263', 'PO_ONLY', '4501607263', '2025-02-21', '2026-02-20', 'ACTIVE', 0);
SET @contract_346 = LAST_INSERT_ID();

-- Contract 347: 4501607265
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_124, '4501607265', 'PO_ONLY', '4501607265', '2025-02-16', '2026-02-15', 'ACTIVE', 0);
SET @contract_347 = LAST_INSERT_ID();

-- Contract 348: 4502190202
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_124, '4502190202', 'PO_ONLY', '4502190202', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_348 = LAST_INSERT_ID();

-- Contract 349: 4501906925
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_124, '4501906925', 'PO_ONLY', '4501906925', '2025-07-29', '2026-07-28', 'ACTIVE', 0);
SET @contract_349 = LAST_INSERT_ID();

-- Contract 350: 4630000867-20371/OKI-SML/V/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_132, '4630000867-20371/OKI-SML/V/2025', 'PO_ONLY', '4630000867-20371/OKI-SML/V/2025', '2025-10-01', '2026-04-30', 'ACTIVE', 0);
SET @contract_350 = LAST_INSERT_ID();

-- Contract 351: 026/MAPIN/ADD.4/GA/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_133, '026/MAPIN/ADD.4/GA/2025', 'PO_ONLY', '026/MAPIN/ADD.4/GA/2025', '2025-07-12', '2026-07-11', 'ACTIVE', 0);
SET @contract_351 = LAST_INSERT_ID();

-- Contract 352: PO/Bulan
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_134, 'PO/Bulan', 'PO_ONLY', 'PO/Bulan', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_352 = LAST_INSERT_ID();

-- Contract 353: Agrement KCC-SML
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_135, 'Agrement KCC-SML', 'PO_ONLY', 'Agrement KCC-SML', '2025-11-01', '2026-12-31', 'ACTIVE', 0);
SET @contract_353 = LAST_INSERT_ID();

-- Contract 354: PO On Progres
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_138, 'PO On Progres', 'PO_ONLY', 'PO On Progres', '2025-12-12', '2026-02-11', 'ACTIVE', 0);
SET @contract_354 = LAST_INSERT_ID();

-- Contract 355: ID1/POR/251000000322
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_140, 'ID1/POR/251000000322', 'PO_ONLY', 'ID1/POR/251000000322', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_355 = LAST_INSERT_ID();

-- Contract 356: 23102014742
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_138, '23102014742', 'PO_ONLY', '23102014742', '2024-10-26', '2025-10-25', 'ACTIVE', 0);
SET @contract_356 = LAST_INSERT_ID();

-- Contract 357: 52048488
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_146, '52048488', 'PO_ONLY', '52048488', '2024-03-01', '2025-03-01', 'PENDING', 0);
SET @contract_357 = LAST_INSERT_ID();

-- Contract 358: 804/SML/I/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_149, '804/SML/I/2025', 'PO_ONLY', '804/SML/I/2025', '2025-02-01', '2028-02-28', 'ACTIVE', 0);
SET @contract_358 = LAST_INSERT_ID();

-- Contract 359: 4500105433
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_117, '4500105433', 'PO_ONLY', '4500105433', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_359 = LAST_INSERT_ID();

-- Contract 360: 156/PO/PINDO-SML/IV/2023
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_152, '156/PO/PINDO-SML/IV/2023', 'PO_ONLY', '156/PO/PINDO-SML/IV/2023', '2023-01-04', '2024-01-04', 'PENDING', 0);
SET @contract_360 = LAST_INSERT_ID();

-- Contract 361: 4600078213
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_113, '4600078213', 'PO_ONLY', '4600078213', '2025-09-06', '2025-12-05', 'ACTIVE', 0);
SET @contract_361 = LAST_INSERT_ID();

-- Contract 362: 193/LGEIN/EESH/IX-21/2020
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_114, '193/LGEIN/EESH/IX-21/2020', 'PO_ONLY', '193/LGEIN/EESH/IX-21/2020', '2021-07-01', '2026-06-30', 'ACTIVE', 0);
SET @contract_362 = LAST_INSERT_ID();

-- Contract 363: 4018011075
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_131, '4018011075', 'PO_ONLY', '4018011075', '2025-07-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_363 = LAST_INSERT_ID();

-- Contract 364: 44966961
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_64, '44966961', 'PO_ONLY', '44966961', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_364 = LAST_INSERT_ID();

-- Contract 365: 4501945653
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_124, '4501945653', 'PO_ONLY', '4501945653', '2025-02-15', '2025-12-14', 'ACTIVE', 0);
SET @contract_365 = LAST_INSERT_ID();

-- Contract 366: 23102015037
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_138, '23102015037', 'PO_ONLY', '23102015037', '2025-01-02', '2026-01-01', 'ACTIVE', 0);
SET @contract_366 = LAST_INSERT_ID();

-- Contract 367: KBN-52056742
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_146, 'KBN-52056742', 'PO_ONLY', 'KBN-52056742', '2025-02-01', '2028-01-31', 'ACTIVE', 0);
SET @contract_367 = LAST_INSERT_ID();

-- Contract 368: KBN-52056736
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_146, 'KBN-52056736', 'PO_ONLY', 'KBN-52056736', '2025-02-01', '2028-01-31', 'ACTIVE', 0);
SET @contract_368 = LAST_INSERT_ID();

-- Contract 369: KBN-52056777
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_146, 'KBN-52056777', 'PO_ONLY', 'KBN-52056777', '2025-02-01', '2028-01-31', 'ACTIVE', 0);
SET @contract_369 = LAST_INSERT_ID();

-- Contract 370: KBN-52056762
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_146, 'KBN-52056762', 'PO_ONLY', 'KBN-52056762', '2025-02-01', '2028-01-31', 'ACTIVE', 0);
SET @contract_370 = LAST_INSERT_ID();

-- Contract 371: 4501631335
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_124, '4501631335', 'PO_ONLY', '4501631335', '2025-05-23', '2026-01-22', 'ACTIVE', 0);
SET @contract_371 = LAST_INSERT_ID();

-- Contract 372: KBN-52056760
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_146, 'KBN-52056760', 'PO_ONLY', 'KBN-52056760', '2025-02-01', '2028-01-31', 'ACTIVE', 0);
SET @contract_372 = LAST_INSERT_ID();

-- Contract 373: 4501827853
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_124, '4501827853', 'PO_ONLY', '4501827853', '2025-07-13', '2025-12-12', 'ACTIVE', 0);
SET @contract_373 = LAST_INSERT_ID();

-- Contract 374: 25301184
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_160, '25301184', 'PO_ONLY', '25301184', '2025-07-14', '2026-07-13', 'ACTIVE', 0);
SET @contract_374 = LAST_INSERT_ID();

-- Contract 375: 4501803896
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_124, '4501803896', 'PO_ONLY', '4501803896', '2025-06-01', '2025-05-01', 'ACTIVE', 0);
SET @contract_375 = LAST_INSERT_ID();

-- Contract 376: 4600071612
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_113, '4600071612', 'PO_ONLY', '4600071612', '2024-09-20', '2025-09-19', 'ACTIVE', 0);
SET @contract_376 = LAST_INSERT_ID();

-- Contract 377: 4600078198
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_113, '4600078198', 'PO_ONLY', '4600078198', '2025-08-29', '2025-11-28', 'ACTIVE', 0);
SET @contract_377 = LAST_INSERT_ID();

-- Contract 378: WF-100148208
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_134, 'WF-100148208', 'PO_ONLY', 'WF-100148208', '2025-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_378 = LAST_INSERT_ID();

-- Contract 379: DL-100151591
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_134, 'DL-100151591', 'PO_ONLY', 'DL-100151591', '2025-02-01', '2026-01-31', 'ACTIVE', 0);
SET @contract_379 = LAST_INSERT_ID();

-- Contract 380: 0278/AMA/05/25
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_162, '0278/AMA/05/25', 'PO_ONLY', '0278/AMA/05/25', '2025-05-09', '2028-05-08', 'ACTIVE', 0);
SET @contract_380 = LAST_INSERT_ID();

-- Contract 381: PO/PAP/23/0107
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_113, 'PO/PAP/23/0107', 'PO_ONLY', 'PO/PAP/23/0107', '2023-06-01', '2026-05-31', 'ACTIVE', 0);
SET @contract_381 = LAST_INSERT_ID();

-- Contract 382: 45013276464
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_124, '45013276464', 'PO_ONLY', '45013276464', '2024-09-24', '2025-03-23', 'ACTIVE', 0);
SET @contract_382 = LAST_INSERT_ID();

-- Contract 383: 71112524
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_146, '71112524', 'PO_ONLY', '71112524', '2024-11-01', '2027-10-31', 'ACTIVE', 0);
SET @contract_383 = LAST_INSERT_ID();

-- Contract 384: SPARE
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_163, 'SPARE', 'PO_ONLY', 'SPARE', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_384 = LAST_INSERT_ID();

-- Contract 385: 594/SML/1/2023
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_115, '594/SML/1/2023', 'PO_ONLY', '594/SML/1/2023', '2023-01-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_385 = LAST_INSERT_ID();

-- Contract 386: 4502179559
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_124, '4502179559', 'PO_ONLY', '4502179559', '2026-01-01', '2026-01-31', 'ACTIVE', 0);
SET @contract_386 = LAST_INSERT_ID();

-- Contract 387: 123/PO/Univ-SML/PSM/VII/2022
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_146, '123/PO/Univ-SML/PSM/VII/2022', 'PO_ONLY', '123/PO/Univ-SML/PSM/VII/2022', '2022-10-04', '2025-06-30', 'ACTIVE', 0);
SET @contract_387 = LAST_INSERT_ID();

-- Contract 388: 426/SML/VII/2021
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_134, '426/SML/VII/2021', 'PO_ONLY', '426/SML/VII/2021', '2026-02-18', '2023-12-08', 'ACTIVE', 0);
SET @contract_388 = LAST_INSERT_ID();

-- Contract 389: 136/PO/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_129, '136/PO/2025', 'PO_ONLY', '136/PO/2025', '2025-11-01', '2026-10-31', 'ACTIVE', 0);
SET @contract_389 = LAST_INSERT_ID();

-- Contract 390: 23102015050
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_138, '23102015050', 'PO_ONLY', '23102015050', '2024-12-30', '2025-12-29', 'ACTIVE', 0);
SET @contract_390 = LAST_INSERT_ID();

-- Contract 391: 4600078212
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_113, '4600078212', 'PO_ONLY', '4600078212', '2025-11-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_391 = LAST_INSERT_ID();

-- Contract 392: 4600071950
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_113, '4600071950', 'PO_ONLY', '4600071950', '2024-11-01', '2025-10-31', 'ACTIVE', 0);
SET @contract_392 = LAST_INSERT_ID();

-- Contract 393: 71112528
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_146, '71112528', 'PO_ONLY', '71112528', '2024-11-01', '2027-10-31', 'ACTIVE', 0);
SET @contract_393 = LAST_INSERT_ID();

-- Contract 394: CKP-71112937
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_146, 'CKP-71112937', 'PO_ONLY', 'CKP-71112937', '2025-03-01', '2028-02-29', 'ACTIVE', 0);
SET @contract_394 = LAST_INSERT_ID();

-- Contract 395: 71112530
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_146, '71112530', 'PO_ONLY', '71112530', '2024-11-01', '2027-10-31', 'ACTIVE', 0);
SET @contract_395 = LAST_INSERT_ID();

-- Contract 396: 71112533
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_146, '71112533', 'PO_ONLY', '71112533', '2024-10-01', '2027-09-30', 'ACTIVE', 0);
SET @contract_396 = LAST_INSERT_ID();

-- Contract 397: 4630000537/20874-22741/OKI-SML/IX/24
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_132, '4630000537/20874-22741/OKI-SML/IX/24', 'PO_ONLY', '4630000537/20874-22741/OKI-SML/IX/24', '2025-05-01', '2028-04-30', 'ACTIVE', 0);
SET @contract_397 = LAST_INSERT_ID();

-- Contract 398: CKP-71112944
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_146, 'CKP-71112944', 'PO_ONLY', 'CKP-71112944', '2025-03-01', '2028-02-29', 'ACTIVE', 0);
SET @contract_398 = LAST_INSERT_ID();

-- Contract 399: 671/SML/XII/2023
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_130, '671/SML/XII/2023', 'PO_ONLY', '671/SML/XII/2023', '2024-05-05', '2026-05-04', 'ACTIVE', 0);
SET @contract_399 = LAST_INSERT_ID();

-- Contract 400: 099/SML/XII/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_167, '099/SML/XII/2025', 'PO_ONLY', '099/SML/XII/2025', '2025-12-03', '2028-12-02', 'ACTIVE', 0);
SET @contract_400 = LAST_INSERT_ID();

-- Contract 401: 82/DSY/1404/25
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_145, '82/DSY/1404/25', 'PO_ONLY', '82/DSY/1404/25', '2025-05-06', '2026-05-05', 'ACTIVE', 0);
SET @contract_401 = LAST_INSERT_ID();

-- Contract 402: 4501619672
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_124, '4501619672', 'PO_ONLY', '4501619672', '2025-03-25', '2026-01-24', 'ACTIVE', 0);
SET @contract_402 = LAST_INSERT_ID();

-- Contract 403: 4610018422/15530/TBU-SML/III/2023 ADD-II
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_132, '4610018422/15530/TBU-SML/III/2023 ADD-II', 'PO_ONLY', '4610018422/15530/TBU-SML/III/2023 ADD-II', '2024-10-01', '2025-08-31', 'ACTIVE', 0);
SET @contract_403 = LAST_INSERT_ID();

-- Contract 404: WF-100159385
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_134, 'WF-100159385', 'PO_ONLY', 'WF-100159385', '2025-01-23', '2026-01-22', 'ACTIVE', 0);
SET @contract_404 = LAST_INSERT_ID();

-- Contract 405: 130/ADD-VIII/TBU-SML/III/2024
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_132, '130/ADD-VIII/TBU-SML/III/2024', 'PO_ONLY', '130/ADD-VIII/TBU-SML/III/2024', '2025-01-16', '2026-01-16', 'PENDING', 0);
SET @contract_405 = LAST_INSERT_ID();

-- Contract 406: 23102015044
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_138, '23102015044', 'PO_ONLY', '23102015044', '2024-12-28', '2025-12-27', 'ACTIVE', 0);
SET @contract_406 = LAST_INSERT_ID();

-- Contract 407: 23102015045
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_138, '23102015045', 'PO_ONLY', '23102015045', '2024-12-28', '2025-12-27', 'ACTIVE', 0);
SET @contract_407 = LAST_INSERT_ID();

-- Contract 408: 23102015046
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_138, '23102015046', 'PO_ONLY', '23102015046', '2024-12-28', '2025-12-27', 'ACTIVE', 0);
SET @contract_408 = LAST_INSERT_ID();

-- Contract 409: 4501803894
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_124, '4501803894', 'PO_ONLY', '4501803894', '2025-06-01', '2025-05-01', 'ACTIVE', 0);
SET @contract_409 = LAST_INSERT_ID();

-- Contract 410: CKP-71112936
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_146, 'CKP-71112936', 'PO_ONLY', 'CKP-71112936', '2025-03-01', '2028-02-29', 'ACTIVE', 0);
SET @contract_410 = LAST_INSERT_ID();

-- Contract 411: CKP-71112943
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_146, 'CKP-71112943', 'PO_ONLY', 'CKP-71112943', '2025-03-01', '2028-02-29', 'ACTIVE', 0);
SET @contract_411 = LAST_INSERT_ID();

-- Contract 412: KBN-52056713
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_146, 'KBN-52056713', 'PO_ONLY', 'KBN-52056713', '2025-02-01', '2028-01-31', 'ACTIVE', 0);
SET @contract_412 = LAST_INSERT_ID();

-- Contract 413: No. 055/SML/VII/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_169, 'No. 055/SML/VII/2025', 'PO_ONLY', 'No. 055/SML/VII/2025', '2025-11-01', '2026-09-30', 'ACTIVE', 0);
SET @contract_413 = LAST_INSERT_ID();

-- Contract 414: 4502084914
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_124, '4502084914', 'PO_ONLY', '4502084914', '2025-10-07', '2026-10-06', 'ACTIVE', 0);
SET @contract_414 = LAST_INSERT_ID();

-- Contract 415: KBN-52056749
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_146, 'KBN-52056749', 'PO_ONLY', 'KBN-52056749', '2025-02-01', '2028-01-31', 'ACTIVE', 0);
SET @contract_415 = LAST_INSERT_ID();

-- Contract 416: 3100016263
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_171, '3100016263', 'PO_ONLY', '3100016263', '2024-12-21', '2025-08-19', 'ACTIVE', 0);
SET @contract_416 = LAST_INSERT_ID();

-- Contract 417: 0741/GMP/11/24
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_172, '0741/GMP/11/24', 'PO_ONLY', '0741/GMP/11/24', '2024-12-04', '2027-12-03', 'ACTIVE', 0);
SET @contract_417 = LAST_INSERT_ID();

-- Contract 418: KBN-52056709
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_146, 'KBN-52056709', 'PO_ONLY', 'KBN-52056709', '2025-02-01', '2028-01-31', 'ACTIVE', 0);
SET @contract_418 = LAST_INSERT_ID();

-- Contract 419: 4630000537/20948/OKI-SML/IX/2024
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_132, '4630000537/20948/OKI-SML/IX/2024', 'PO_ONLY', '4630000537/20948/OKI-SML/IX/2024', '2024-12-01', '2027-11-30', 'ACTIVE', 0);
SET @contract_419 = LAST_INSERT_ID();

-- Contract 420: 4630000537/21146/OKI-SML/IX/2024
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_132, '4630000537/21146/OKI-SML/IX/2024', 'PO_ONLY', '4630000537/21146/OKI-SML/IX/2024', '2024-12-01', '2027-11-30', 'ACTIVE', 0);
SET @contract_420 = LAST_INSERT_ID();

-- Contract 421: 23102014975
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_138, '23102014975', 'PO_ONLY', '23102014975', '2024-12-23', '2025-12-22', 'ACTIVE', 0);
SET @contract_421 = LAST_INSERT_ID();

-- Contract 422: 23102015039
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_138, '23102015039', 'PO_ONLY', '23102015039', '2024-12-27', '2025-12-26', 'ACTIVE', 0);
SET @contract_422 = LAST_INSERT_ID();

-- Contract 423: 23102015040
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_138, '23102015040', 'PO_ONLY', '23102015040', '2024-12-27', '2025-12-26', 'ACTIVE', 0);
SET @contract_423 = LAST_INSERT_ID();

-- Contract 424: 23102015041
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_138, '23102015041', 'PO_ONLY', '23102015041', '2024-12-27', '2025-12-26', 'ACTIVE', 0);
SET @contract_424 = LAST_INSERT_ID();

-- Contract 425: 23102015042
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_138, '23102015042', 'PO_ONLY', '23102015042', '2024-12-27', '2025-12-26', 'ACTIVE', 0);
SET @contract_425 = LAST_INSERT_ID();

-- Contract 426: 4600069789
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_113, '4600069789', 'PO_ONLY', '4600069789', '2024-07-01', '2026-05-31', 'ACTIVE', 0);
SET @contract_426 = LAST_INSERT_ID();

-- Contract 427: 4600069790
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_113, '4600069790', 'PO_ONLY', '4600069790', '2024-07-01', '2026-05-31', 'ACTIVE', 0);
SET @contract_427 = LAST_INSERT_ID();

-- Contract 428: 4600069792
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_113, '4600069792', 'PO_ONLY', '4600069792', '2024-07-01', '2026-05-31', 'ACTIVE', 0);
SET @contract_428 = LAST_INSERT_ID();

-- Contract 429: 23102015048
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_138, '23102015048', 'PO_ONLY', '23102015048', '2024-12-30', '2025-12-29', 'ACTIVE', 0);
SET @contract_429 = LAST_INSERT_ID();

-- Contract 430: 23102015049
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_138, '23102015049', 'PO_ONLY', '23102015049', '2024-12-30', '2025-12-29', 'ACTIVE', 0);
SET @contract_430 = LAST_INSERT_ID();

-- Contract 431: Add No C2023025677
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_114, 'Add No C2023025677', 'PO_ONLY', 'Add No C2023025677', '2023-06-27', '2026-06-26', 'ACTIVE', 0);
SET @contract_431 = LAST_INSERT_ID();

-- Contract 432: KBN-52056741
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_146, 'KBN-52056741', 'PO_ONLY', 'KBN-52056741', '2025-02-01', '2028-01-31', 'ACTIVE', 0);
SET @contract_432 = LAST_INSERT_ID();

-- Contract 433: 119/LEGASTRO/OPS/VII/2025 Add 1
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_140, '119/LEGASTRO/OPS/VII/2025 Add 1', 'PO_ONLY', '119/LEGASTRO/OPS/VII/2025 Add 1', '2025-07-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_433 = LAST_INSERT_ID();

-- Contract 434: 4600069797
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_113, '4600069797', 'PO_ONLY', '4600069797', '2024-07-01', '2026-05-31', 'ACTIVE', 0);
SET @contract_434 = LAST_INSERT_ID();

-- Contract 435: KBN-52056768
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_146, 'KBN-52056768', 'PO_ONLY', 'KBN-52056768', '2025-02-01', '2028-01-31', 'ACTIVE', 0);
SET @contract_435 = LAST_INSERT_ID();

-- Contract 436: 0741/GMP/08/24
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_174, '0741/GMP/08/24', 'PO_ONLY', '0741/GMP/08/24', '2025-02-07', '2028-02-06', 'ACTIVE', 0);
SET @contract_436 = LAST_INSERT_ID();

-- Contract 437: 747/SML/IX/2024
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_175, '747/SML/IX/2024', 'PO_ONLY', '747/SML/IX/2024', '2026-01-01', '2026-12-31', 'ACTIVE', 0);
SET @contract_437 = LAST_INSERT_ID();

-- Contract 438: Service Agreement 01 Maret 2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_176, 'Service Agreement 01 Maret 2025', 'PO_ONLY', 'Service Agreement 01 Maret 2025', '2025-05-15', '2030-05-31', 'ACTIVE', 0);
SET @contract_438 = LAST_INSERT_ID();

-- Contract 439: 091/SML/X/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_176, '091/SML/X/2025', 'PO_ONLY', '091/SML/X/2025', '2025-12-01', '2026-11-30', 'ACTIVE', 0);
SET @contract_439 = LAST_INSERT_ID();

-- Contract 440: 4501326057
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_124, '4501326057', 'PO_ONLY', '4501326057', '2025-09-26', '2025-03-25', 'ACTIVE', 0);
SET @contract_440 = LAST_INSERT_ID();

-- Contract 441: 732-SML-VII-2024
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_113, '732-SML-VII-2024', 'PO_ONLY', '732-SML-VII-2024', '2024-07-01', '2027-06-30', 'ACTIVE', 0);
SET @contract_441 = LAST_INSERT_ID();

-- Contract 442: 4501955707
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_124, '4501955707', 'PO_ONLY', '4501955707', '2025-08-26', '2026-06-27', 'ACTIVE', 0);
SET @contract_442 = LAST_INSERT_ID();

-- Contract 443: 4600074960
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_113, '4600074960', 'PO_ONLY', '4600074960', '2025-05-01', '2028-04-30', 'ACTIVE', 0);
SET @contract_443 = LAST_INSERT_ID();

-- Contract 444: 320/SPD/PINDO/SML/I/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_152, '320/SPD/PINDO/SML/I/2025', 'PO_ONLY', '320/SPD/PINDO/SML/I/2025', '2025-01-01', '2026-01-01', 'PENDING', 0);
SET @contract_444 = LAST_INSERT_ID();

-- Contract 445: KBN-52056706
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_146, 'KBN-52056706', 'PO_ONLY', 'KBN-52056706', '2025-02-01', '2028-01-31', 'ACTIVE', 0);
SET @contract_445 = LAST_INSERT_ID();

-- Contract 446: 4600074961
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_113, '4600074961', 'PO_ONLY', '4600074961', '2025-05-01', '2028-04-30', 'ACTIVE', 0);
SET @contract_446 = LAST_INSERT_ID();

-- Contract 447: 025/SML-IDR/IV/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_178, '025/SML-IDR/IV/2025', 'PO_ONLY', '025/SML-IDR/IV/2025', '0205-04-28', '2028-04-27', 'ACTIVE', 0);
SET @contract_447 = LAST_INSERT_ID();

-- Contract 448: 723-SML-VII-2024
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_113, '723-SML-VII-2024', 'PO_ONLY', '723-SML-VII-2024', '2024-07-01', '2027-06-30', 'ACTIVE', 0);
SET @contract_448 = LAST_INSERT_ID();

-- Contract 449: 130/DSY/1106/25
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_145, '130/DSY/1106/25', 'PO_ONLY', '130/DSY/1106/25', '2025-06-17', '2026-06-16', 'ACTIVE', 0);
SET @contract_449 = LAST_INSERT_ID();

-- Contract 450: Agreement Heinz
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_115, 'Agreement Heinz', 'PO_ONLY', 'Agreement Heinz', '2023-07-01', '2025-06-30', 'ACTIVE', 0);
SET @contract_450 = LAST_INSERT_ID();

-- Contract 451: 088/YIDN-05/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_179, '088/YIDN-05/2025', 'PO_ONLY', '088/YIDN-05/2025', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_451 = LAST_INSERT_ID();

-- Contract 452: Spare
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_180, 'Spare', 'PO_ONLY', 'Spare', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_452 = LAST_INSERT_ID();

-- Contract 453: 035/SML/VI/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_181, '035/SML/VI/2025', 'PO_ONLY', '035/SML/VI/2025', '2025-03-31', '2026-03-30', 'ACTIVE', 0);
SET @contract_453 = LAST_INSERT_ID();

-- Contract 454: 0174/SNI/04/25
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_174, '0174/SNI/04/25', 'PO_ONLY', '0174/SNI/04/25', '2025-04-15', '2028-04-14', 'ACTIVE', 0);
SET @contract_454 = LAST_INSERT_ID();

-- Contract 455: 0231/IMLI/04/25
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_182, '0231/IMLI/04/25', 'PO_ONLY', '0231/IMLI/04/25', '2025-05-22', '2028-05-24', 'ACTIVE', 0);
SET @contract_455 = LAST_INSERT_ID();

-- Contract 456: 351/SPD/PINDO-SML/PSM/XI/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_152, '351/SPD/PINDO-SML/PSM/XI/2025', 'PO_ONLY', '351/SPD/PINDO-SML/PSM/XI/2025', '2025-01-10', '2026-01-10', 'PENDING', 0);
SET @contract_456 = LAST_INSERT_ID();

-- Contract 457: 1160006557
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_183, '1160006557', 'PO_ONLY', '1160006557', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_457 = LAST_INSERT_ID();

-- Contract 458: 1160006558
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_183, '1160006558', 'PO_ONLY', '1160006558', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_458 = LAST_INSERT_ID();

-- Contract 459: 4600075415
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_113, '4600075415', 'PO_ONLY', '4600075415', '2025-05-21', '2028-05-31', 'ACTIVE', 0);
SET @contract_459 = LAST_INSERT_ID();

-- Contract 460: 0742/GMP/08/24
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_174, '0742/GMP/08/24', 'PO_ONLY', '0742/GMP/08/24', '2024-11-01', '2027-10-31', 'ACTIVE', 0);
SET @contract_460 = LAST_INSERT_ID();

-- Contract 461: 164/DSY/1907/25
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_145, '164/DSY/1907/25', 'PO_ONLY', '164/DSY/1907/25', '2025-07-28', '2026-07-27', 'ACTIVE', 0);
SET @contract_461 = LAST_INSERT_ID();

-- Contract 462: 4501963218
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_124, '4501963218', 'PO_ONLY', '4501963218', '2025-09-26', '2026-06-25', 'ACTIVE', 0);
SET @contract_462 = LAST_INSERT_ID();

-- Contract 463: 049/SML/VII/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_167, '049/SML/VII/2025', 'PO_ONLY', '049/SML/VII/2025', '2025-08-01', '2028-07-31', 'ACTIVE', 0);
SET @contract_463 = LAST_INSERT_ID();

-- Contract 464: 4501607261
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_124, '4501607261', 'PO_ONLY', '4501607261', '2025-12-20', '2026-12-19', 'ACTIVE', 0);
SET @contract_464 = LAST_INSERT_ID();

-- Contract 465: DSY/2508/0008
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_145, 'DSY/2508/0008', 'PO_ONLY', 'DSY/2508/0008', '2025-08-11', '2026-08-11', 'ACTIVE', 0);
SET @contract_465 = LAST_INSERT_ID();

-- Contract 466: 0570/SNI/10/25
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_174, '0570/SNI/10/25', 'PO_ONLY', '0570/SNI/10/25', '2025-11-14', '2028-11-13', 'ACTIVE', 0);
SET @contract_466 = LAST_INSERT_ID();

-- Contract 467: 030/BIK/I/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_138, '030/BIK/I/2025', 'PO_ONLY', '030/BIK/I/2025', '2025-09-04', '2026-03-03', 'ACTIVE', 0);
SET @contract_467 = LAST_INSERT_ID();

-- Contract 468: 71113679
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_146, '71113679', 'PO_ONLY', '71113679', '2025-09-17', '2026-02-16', 'ACTIVE', 0);
SET @contract_468 = LAST_INSERT_ID();

-- Contract 469: DSY/2509/003
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_145, 'DSY/2509/003', 'PO_ONLY', 'DSY/2509/003', '2025-09-16', '2026-09-16', 'ACTIVE', 0);
SET @contract_469 = LAST_INSERT_ID();

-- Contract 470: 0424/AMA/08/24
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_162, '0424/AMA/08/24', 'PO_ONLY', '0424/AMA/08/24', '2024-09-01', '2027-08-31', 'ACTIVE', 0);
SET @contract_470 = LAST_INSERT_ID();

-- Contract 471: 0748/IMLI/10/25
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_182, '0748/IMLI/10/25', 'PO_ONLY', '0748/IMLI/10/25', '2025-10-10', '2028-10-09', 'ACTIVE', 0);
SET @contract_471 = LAST_INSERT_ID();

-- Contract 472: DDY/2509/0016
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_145, 'DDY/2509/0016', 'PO_ONLY', 'DDY/2509/0016', '2025-10-15', '2026-10-15', 'ACTIVE', 0);
SET @contract_472 = LAST_INSERT_ID();

-- Contract 473: 4600079099
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_113, '4600079099', 'PO_ONLY', '4600079099', '2025-11-28', '2028-11-27', 'ACTIVE', 0);
SET @contract_473 = LAST_INSERT_ID();

-- Contract 474: 4600079101
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_113, '4600079101', 'PO_ONLY', '4600079101', '2025-11-28', '2028-11-27', 'ACTIVE', 0);
SET @contract_474 = LAST_INSERT_ID();

-- Contract 475: WF-100152251
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_134, 'WF-100152251', 'PO_ONLY', 'WF-100152251', '2025-01-27', '2026-01-26', 'ACTIVE', 0);
SET @contract_475 = LAST_INSERT_ID();

-- Contract 476: 4501619671
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_124, '4501619671', 'PO_ONLY', '4501619671', '2025-01-15', '2025-12-14', 'ACTIVE', 0);
SET @contract_476 = LAST_INSERT_ID();

-- Contract 477: UPP-310025075
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_149, 'UPP-310025075', 'PO_ONLY', 'UPP-310025075', '2025-07-20', '2025-12-19', 'ACTIVE', 0);
SET @contract_477 = LAST_INSERT_ID();

-- Contract 478: 23102017202
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_138, '23102017202', 'PO_ONLY', '23102017202', '2025-08-27', '2026-08-26', 'ACTIVE', 0);
SET @contract_478 = LAST_INSERT_ID();

-- Contract 479: 23102016769
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_138, '23102016769', 'PO_ONLY', '23102016769', '2025-09-10', '2025-10-09', 'ACTIVE', 0);
SET @contract_479 = LAST_INSERT_ID();

-- Contract 480: 074/SML/IX/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_138, '074/SML/IX/2025', 'PO_ONLY', '074/SML/IX/2025', '2025-08-27', '2026-08-26', 'ACTIVE', 0);
SET @contract_480 = LAST_INSERT_ID();

-- Contract 481: 4501631334
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_124, '4501631334', 'PO_ONLY', '4501631334', '2025-01-20', '2026-01-19', 'ACTIVE', 0);
SET @contract_481 = LAST_INSERT_ID();

-- Contract 482: KBN-52056717
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_146, 'KBN-52056717', 'PO_ONLY', 'KBN-52056717', '2025-02-01', '2028-01-31', 'ACTIVE', 0);
SET @contract_482 = LAST_INSERT_ID();

-- Contract 483: KBN-52056781
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_146, 'KBN-52056781', 'PO_ONLY', 'KBN-52056781', '2025-02-01', '2028-01-31', 'ACTIVE', 0);
SET @contract_483 = LAST_INSERT_ID();

-- Contract 484: 01050/IMLI/09/24
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_182, '01050/IMLI/09/24', 'PO_ONLY', '01050/IMLI/09/24', '2024-01-17', '2027-01-16', 'ACTIVE', 0);
SET @contract_484 = LAST_INSERT_ID();

-- Contract 485: ID1/POR/251000000321
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_140, 'ID1/POR/251000000321', 'PO_ONLY', 'ID1/POR/251000000321', '2025-10-23', '2026-05-22', 'ACTIVE', 0);
SET @contract_485 = LAST_INSERT_ID();

-- Contract 486: 5000000516
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_112, '5000000516', 'PO_ONLY', '5000000516', '2025-07-01', '2025-12-31', 'ACTIVE', 0);
SET @contract_486 = LAST_INSERT_ID();

-- Contract 487: 092/SML/X/2025
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_138, '092/SML/X/2025', 'PO_ONLY', '092/SML/X/2025', '2025-11-04', '2030-11-03', 'ACTIVE', 0);
SET @contract_487 = LAST_INSERT_ID();

-- Contract 488: 23102015047
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_138, '23102015047', 'PO_ONLY', '23102015047', '2024-12-27', '2025-12-26', 'ACTIVE', 0);
SET @contract_488 = LAST_INSERT_ID();

-- Contract 489: 4501829496
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_124, '4501829496', 'PO_ONLY', '4501829496', '2025-07-04', '2025-12-03', 'ACTIVE', 0);
SET @contract_489 = LAST_INSERT_ID();

-- Contract 490: 0095/AMA/02/26
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_162, '0095/AMA/02/26', 'PO_ONLY', '0095/AMA/02/26', '2026-02-05', '2029-02-04', 'ACTIVE', 0);
SET @contract_490 = LAST_INSERT_ID();

-- Contract 491: 3800724317
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (334, '3800724317', 'PO_ONLY', '3800724317', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_491 = LAST_INSERT_ID();

-- Contract 492: 71113429
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (125, '71113429', 'PO_ONLY', '71113429', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_492 = LAST_INSERT_ID();

-- Contract 493: (perpanjangn kontrak 017 per'Juli 25)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (125, '(perpanjangn kontrak 017 per''Juli 25)', 'PO_ONLY', '(perpanjangn kontrak 017 per''Juli 25)', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_493 = LAST_INSERT_ID();

-- Contract 494: 4507148105
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (436, '4507148105', 'PO_ONLY', '4507148105', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_494 = LAST_INSERT_ID();

-- Contract 495: 4032006905
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (386, '4032006905', 'PO_ONLY', '4032006905', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_495 = LAST_INSERT_ID();

-- Contract 496: 71112345
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (125, '71112345', 'PO_ONLY', '71112345', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_496 = LAST_INSERT_ID();

-- Contract 497: 6190000954
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_194, '6190000954', 'PO_ONLY', '6190000954', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_497 = LAST_INSERT_ID();

-- Contract 498: RH #3 / FS #1 (SELATAN)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (125, 'RH #3 / FS #1 (SELATAN)', 'PO_ONLY', 'RH #3 / FS #1 (SELATAN)', '2023-06-01', '2026-05-31', 'ACTIVE', 0);
SET @contract_498 = LAST_INSERT_ID();

-- Contract 499: 612/SML/V/2023
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_197, '612/SML/V/2023', 'PO_ONLY', '612/SML/V/2023', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_499 = LAST_INSERT_ID();

-- Contract 500: PPM#1 (SELATAN)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (125, 'PPM#1 (SELATAN)', 'PO_ONLY', 'PPM#1 (SELATAN)', '2023-06-01', '2026-05-31', 'ACTIVE', 0);
SET @contract_500 = LAST_INSERT_ID();

-- Contract 501: USER (BPK SUGIANTO) - (4 INVOICE)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (125, 'USER (BPK SUGIANTO) - (4 INVOICE)', 'PO_ONLY', 'USER (BPK SUGIANTO) - (4 INVOICE)', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_501 = LAST_INSERT_ID();

-- Contract 502: 3800741865
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (334, '3800741865', 'PO_ONLY', '3800741865', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_502 = LAST_INSERT_ID();

-- Contract 503: 3800741864
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (334, '3800741864', 'PO_ONLY', '3800741864', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_503 = LAST_INSERT_ID();

-- Contract 504: 3800741863
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (334, '3800741863', 'PO_ONLY', '3800741863', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_504 = LAST_INSERT_ID();

-- Contract 505: 3800741862
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (334, '3800741862', 'PO_ONLY', '3800741862', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_505 = LAST_INSERT_ID();

-- Contract 506: 3800739180
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (334, '3800739180', 'PO_ONLY', '3800739180', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_506 = LAST_INSERT_ID();

-- Contract 507: PO
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_198, 'PO', 'PO_ONLY', 'PO', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_507 = LAST_INSERT_ID();

-- Contract 508: 015/GTL-PO/IX/2023
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_197, '015/GTL-PO/IX/2023', 'PO_ONLY', '015/GTL-PO/IX/2023', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_508 = LAST_INSERT_ID();

-- Contract 509: 3800736347
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (337, '3800736347', 'PO_ONLY', '3800736347', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_509 = LAST_INSERT_ID();

-- Contract 510: 4531003784
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (305, '4531003784', 'PO_ONLY', '4531003784', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_510 = LAST_INSERT_ID();

-- Contract 511: USER (BPK SUGIANTO)- (5 INVOICE)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (125, 'USER (BPK SUGIANTO)- (5 INVOICE)', 'PO_ONLY', 'USER (BPK SUGIANTO)- (5 INVOICE)', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_511 = LAST_INSERT_ID();

-- Contract 512: AM #1 (SELATAN)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (125, 'AM #1 (SELATAN)', 'PO_ONLY', 'AM #1 (SELATAN)', '2023-06-01', '2026-05-31', 'ACTIVE', 0);
SET @contract_512 = LAST_INSERT_ID();

-- Contract 513: 3800724340
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (334, '3800724340', 'PO_ONLY', '3800724340', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_513 = LAST_INSERT_ID();

-- Contract 514: RH #2 (UTARA)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (125, 'RH #2 (UTARA)', 'PO_ONLY', 'RH #2 (UTARA)', '2023-06-01', '2026-05-31', 'ACTIVE', 0);
SET @contract_514 = LAST_INSERT_ID();

-- Contract 515: 4506906882
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (163, '4506906882', 'PO_ONLY', '4506906882', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_515 = LAST_INSERT_ID();

-- Contract 516: 71112417
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (125, '71112417', 'PO_ONLY', '71112417', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_516 = LAST_INSERT_ID();

-- Contract 517: RH #1 (SELATAN)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (125, 'RH #1 (SELATAN)', 'PO_ONLY', 'RH #1 (SELATAN)', '2023-06-01', '2026-05-31', 'ACTIVE', 0);
SET @contract_517 = LAST_INSERT_ID();

-- Contract 518: 3800701821
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (334, '3800701821', 'PO_ONLY', '3800701821', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_518 = LAST_INSERT_ID();

-- Contract 519: 3800701913
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (334, '3800701913', 'PO_ONLY', '3800701913', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_519 = LAST_INSERT_ID();

-- Contract 520: 3800716136
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (334, '3800716136', 'PO_ONLY', '3800716136', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_520 = LAST_INSERT_ID();

-- Contract 521: INFO MBA DEIS AKAN PAKAI KONTRAK (6 BLN)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_200, 'INFO MBA DEIS AKAN PAKAI KONTRAK (6 BLN)', 'PO_ONLY', 'INFO MBA DEIS AKAN PAKAI KONTRAK (6 BLN)', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_521 = LAST_INSERT_ID();

-- Contract 522: 71112418
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (125, '71112418', 'PO_ONLY', '71112418', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_522 = LAST_INSERT_ID();

-- Contract 523: LPG-71112832
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_201, 'LPG-71112832', 'PO_ONLY', 'LPG-71112832', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_523 = LAST_INSERT_ID();

-- Contract 524: 4800001899
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (437, '4800001899', 'PO_ONLY', '4800001899', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_524 = LAST_INSERT_ID();

-- Contract 525: GIT/26/010110
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (309, 'GIT/26/010110', 'PO_ONLY', 'GIT/26/010110', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_525 = LAST_INSERT_ID();

-- Contract 526: PPM #4 / PPM #9 (UTARA)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (125, 'PPM #4 / PPM #9 (UTARA)', 'PO_ONLY', 'PPM #4 / PPM #9 (UTARA)', '2023-06-01', '2026-05-31', 'ACTIVE', 0);
SET @contract_526 = LAST_INSERT_ID();

-- Contract 527: PAPER CORE (SELATAN)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (125, 'PAPER CORE (SELATAN)', 'PO_ONLY', 'PAPER CORE (SELATAN)', '2023-06-01', '2026-05-31', 'ACTIVE', 0);
SET @contract_527 = LAST_INSERT_ID();

-- Contract 528: PRINTING (PW)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (125, 'PRINTING (PW)', 'PO_ONLY', 'PRINTING (PW)', '2023-06-01', '2026-05-31', 'ACTIVE', 0);
SET @contract_528 = LAST_INSERT_ID();

-- Contract 529: PPM #3 / PPM #5 (UTARA)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (125, 'PPM #3 / PPM #5 (UTARA)', 'PO_ONLY', 'PPM #3 / PPM #5 (UTARA)', '2023-06-01', '2026-05-31', 'ACTIVE', 0);
SET @contract_529 = LAST_INSERT_ID();

-- Contract 530: Unit Baru (Belum terima PO Per Jan'26)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (390, 'Unit Baru (Belum terima PO Per Jan''26)', 'PO_ONLY', 'Unit Baru (Belum terima PO Per Jan''26)', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_530 = LAST_INSERT_ID();

-- Contract 531: 3800739766
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (334, '3800739766', 'PO_ONLY', '3800739766', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_531 = LAST_INSERT_ID();

-- Contract 532: USER (BPK SUGIANTO)- (4 INVOICE)
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, customer_po_number, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (125, 'USER (BPK SUGIANTO)- (4 INVOICE)', 'PO_ONLY', 'USER (BPK SUGIANTO)- (4 INVOICE)', '2026-02-18', '2027-02-18', 'PENDING', 0);
SET @contract_532 = LAST_INSERT_ID();

-- ========================================
-- STEP 4: UPDATE INVENTORY UNITS
-- Total: 2178 units
-- ========================================

UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 266,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 7700000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2053;
UPDATE inventory_unit SET
  customer_id = 118,
  customer_location_id = 322,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2021-08-21',
  rate_changed_at = NOW()
WHERE no_unit = 2062;
UPDATE inventory_unit SET
  customer_id = 125,
  customer_location_id = 336,
  kontrak_id = @contract_2,
  area_id = NULL,
  harga_sewa_bulanan = 11400000.0,
  on_hire_date = '2025-04-14',
  rate_changed_at = NOW()
WHERE no_unit = 5295;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 377,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 9300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2066;
UPDATE inventory_unit SET
  customer_id = 125,
  customer_location_id = 336,
  kontrak_id = @contract_2,
  area_id = NULL,
  harga_sewa_bulanan = 11400000.0,
  on_hire_date = '2025-04-28',
  rate_changed_at = NOW()
WHERE no_unit = 5296;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 264,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 10050000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2088;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 123,
  kontrak_id = @contract_3,
  area_id = 17,
  harga_sewa_bulanan = 7700.0,
  on_hire_date = '2025-07-29',
  rate_changed_at = NOW()
WHERE no_unit = 5300;
UPDATE inventory_unit SET
  customer_id = 228,
  customer_location_id = @location_1,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2111;
UPDATE inventory_unit SET
  customer_id = 24,
  customer_location_id = @location_2,
  kontrak_id = @contract_4,
  area_id = NULL,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = '2025-03-17',
  rate_changed_at = NOW()
WHERE no_unit = 5585;
UPDATE inventory_unit SET
  customer_id = 88,
  customer_location_id = 259,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 18000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2131;
UPDATE inventory_unit SET
  customer_id = 14,
  customer_location_id = 70,
  kontrak_id = @contract_5,
  area_id = NULL,
  harga_sewa_bulanan = 9500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2136;
UPDATE inventory_unit SET
  customer_id = 94,
  customer_location_id = 275,
  kontrak_id = @contract_6,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2022-12-14',
  rate_changed_at = NOW()
WHERE no_unit = 2149;
UPDATE inventory_unit SET
  customer_id = 71,
  customer_location_id = 216,
  kontrak_id = @contract_7,
  area_id = NULL,
  harga_sewa_bulanan = 5850000.0,
  on_hire_date = '2022-02-23',
  rate_changed_at = NOW()
WHERE no_unit = 2150;
UPDATE inventory_unit SET
  customer_id = 79,
  customer_location_id = 228,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2022-04-04',
  rate_changed_at = NOW()
WHERE no_unit = 2152;
UPDATE inventory_unit SET
  customer_id = 71,
  customer_location_id = 216,
  kontrak_id = @contract_7,
  area_id = NULL,
  harga_sewa_bulanan = 5850000.0,
  on_hire_date = '2022-02-23',
  rate_changed_at = NOW()
WHERE no_unit = 2155;
UPDATE inventory_unit SET
  customer_id = 86,
  customer_location_id = @location_3,
  kontrak_id = @contract_8,
  area_id = NULL,
  harga_sewa_bulanan = 5500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2161;
UPDATE inventory_unit SET
  customer_id = 177,
  customer_location_id = 422,
  kontrak_id = @contract_9,
  area_id = 21,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2162;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = @contract_10,
  area_id = NULL,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = '2025-07-14',
  rate_changed_at = NOW()
WHERE no_unit = 5722;
UPDATE inventory_unit SET
  customer_id = 49,
  customer_location_id = 163,
  kontrak_id = @contract_11,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2025-08-05',
  rate_changed_at = NOW()
WHERE no_unit = 2166;
UPDATE inventory_unit SET
  customer_id = 71,
  customer_location_id = 216,
  kontrak_id = @contract_7,
  area_id = NULL,
  harga_sewa_bulanan = 5850000.0,
  on_hire_date = '2022-02-23',
  rate_changed_at = NOW()
WHERE no_unit = 2167;
UPDATE inventory_unit SET
  customer_id = 86,
  customer_location_id = @location_3,
  kontrak_id = @contract_8,
  area_id = NULL,
  harga_sewa_bulanan = 5500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2170;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 141,
  kontrak_id = @contract_10,
  area_id = NULL,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = '2025-07-14',
  rate_changed_at = NOW()
WHERE no_unit = 5723;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = @contract_10,
  area_id = NULL,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = '2025-07-14',
  rate_changed_at = NOW()
WHERE no_unit = 5724;
UPDATE inventory_unit SET
  customer_id = 177,
  customer_location_id = 422,
  kontrak_id = @contract_9,
  area_id = 21,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2176;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = @contract_12,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2024-12-30',
  rate_changed_at = NOW()
WHERE no_unit = 2180;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_5,
  kontrak_id = @contract_13,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5725;
UPDATE inventory_unit SET
  customer_id = 162,
  customer_location_id = 404,
  kontrak_id = @contract_14,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5729;
UPDATE inventory_unit SET
  customer_id = 166,
  customer_location_id = 408,
  kontrak_id = @contract_15,
  area_id = NULL,
  harga_sewa_bulanan = 6750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2211;
UPDATE inventory_unit SET
  customer_id = 166,
  customer_location_id = 408,
  kontrak_id = @contract_15,
  area_id = NULL,
  harga_sewa_bulanan = 6750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2212;
UPDATE inventory_unit SET
  customer_id = 166,
  customer_location_id = 408,
  kontrak_id = @contract_15,
  area_id = NULL,
  harga_sewa_bulanan = 7200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2214;
UPDATE inventory_unit SET
  customer_id = 166,
  customer_location_id = 408,
  kontrak_id = @contract_15,
  area_id = NULL,
  harga_sewa_bulanan = 6750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2228;
UPDATE inventory_unit SET
  customer_id = 64,
  customer_location_id = @location_6,
  kontrak_id = @contract_16,
  area_id = NULL,
  harga_sewa_bulanan = 10800000.0,
  on_hire_date = '2024-08-27',
  rate_changed_at = NOW()
WHERE no_unit = 2232;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 376,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 3150000.0,
  on_hire_date = '2025-01-18',
  rate_changed_at = NOW()
WHERE no_unit = 2235;
UPDATE inventory_unit SET
  customer_id = 24,
  customer_location_id = @location_2,
  kontrak_id = @contract_17,
  area_id = NULL,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = '2025-03-17',
  rate_changed_at = NOW()
WHERE no_unit = 5586;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 266,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2244;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 379,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2024-01-18',
  rate_changed_at = NOW()
WHERE no_unit = 2245;
UPDATE inventory_unit SET
  customer_id = 82,
  customer_location_id = 234,
  kontrak_id = NULL,
  area_id = 33,
  harga_sewa_bulanan = 19500000.0,
  on_hire_date = '2025-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 6032;
UPDATE inventory_unit SET
  customer_id = 166,
  customer_location_id = 408,
  kontrak_id = @contract_15,
  area_id = NULL,
  harga_sewa_bulanan = 6750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2248;
UPDATE inventory_unit SET
  customer_id = 161,
  customer_location_id = 403,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2250;
UPDATE inventory_unit SET
  customer_id = 24,
  customer_location_id = @location_2,
  kontrak_id = @contract_18,
  area_id = 33,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = '2025-08-02',
  rate_changed_at = NOW()
WHERE no_unit = 5739;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 376,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 3250000.0,
  on_hire_date = '2021-08-12',
  rate_changed_at = NOW()
WHERE no_unit = 2259;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 49,
  kontrak_id = @contract_19,
  area_id = NULL,
  harga_sewa_bulanan = 9900000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2262;
UPDATE inventory_unit SET
  customer_id = 30,
  customer_location_id = 99,
  kontrak_id = @contract_20,
  area_id = 33,
  harga_sewa_bulanan = 16200000.0,
  on_hire_date = '2025-12-13',
  rate_changed_at = NOW()
WHERE no_unit = 6033;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 264,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 15300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2278;
UPDATE inventory_unit SET
  customer_id = 24,
  customer_location_id = @location_7,
  kontrak_id = @contract_21,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = '2025-07-29',
  rate_changed_at = NOW()
WHERE no_unit = 2280;
UPDATE inventory_unit SET
  customer_id = 24,
  customer_location_id = @location_2,
  kontrak_id = @contract_22,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2024-10-07',
  rate_changed_at = NOW()
WHERE no_unit = 2282;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_23,
  area_id = 26,
  harga_sewa_bulanan = 22500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5746;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 132,
  kontrak_id = @contract_12,
  area_id = NULL,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2025-08-19',
  rate_changed_at = NOW()
WHERE no_unit = 5747;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 376,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 3250000.0,
  on_hire_date = '2021-08-06',
  rate_changed_at = NOW()
WHERE no_unit = 2293;
UPDATE inventory_unit SET
  customer_id = 1,
  customer_location_id = @location_9,
  kontrak_id = @contract_24,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2022-06-09',
  rate_changed_at = NOW()
WHERE no_unit = 2294;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = @contract_12,
  area_id = NULL,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2023-06-24',
  rate_changed_at = NOW()
WHERE no_unit = 5748;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 264,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 10050000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2297;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = @contract_12,
  area_id = NULL,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2023-05-31',
  rate_changed_at = NOW()
WHERE no_unit = 5749;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 8400000.0,
  on_hire_date = '2025-02-26',
  rate_changed_at = NOW()
WHERE no_unit = 5343;
UPDATE inventory_unit SET
  customer_id = 145,
  customer_location_id = 383,
  kontrak_id = @contract_26,
  area_id = NULL,
  harga_sewa_bulanan = 30000000.0,
  on_hire_date = '2022-07-04',
  rate_changed_at = NOW()
WHERE no_unit = 2309;
UPDATE inventory_unit SET
  customer_id = 122,
  customer_location_id = 329,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2024-10-03',
  rate_changed_at = NOW()
WHERE no_unit = 5344;
UPDATE inventory_unit SET
  customer_id = 145,
  customer_location_id = 383,
  kontrak_id = @contract_27,
  area_id = NULL,
  harga_sewa_bulanan = 30000000.0,
  on_hire_date = '2025-03-27',
  rate_changed_at = NOW()
WHERE no_unit = 2314;
UPDATE inventory_unit SET
  customer_id = 145,
  customer_location_id = 383,
  kontrak_id = @contract_27,
  area_id = NULL,
  harga_sewa_bulanan = 30000000.0,
  on_hire_date = '2025-06-24',
  rate_changed_at = NOW()
WHERE no_unit = 2315;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 112,
  kontrak_id = @contract_28,
  area_id = NULL,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2025-08-21',
  rate_changed_at = NOW()
WHERE no_unit = 5752;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 8400000.0,
  on_hire_date = '2025-02-25',
  rate_changed_at = NOW()
WHERE no_unit = 2317;
UPDATE inventory_unit SET
  customer_id = 137,
  customer_location_id = 356,
  kontrak_id = @contract_29,
  area_id = 33,
  harga_sewa_bulanan = 11800000.0,
  on_hire_date = '2023-10-07',
  rate_changed_at = NOW()
WHERE no_unit = 2319;
UPDATE inventory_unit SET
  customer_id = 30,
  customer_location_id = 99,
  kontrak_id = @contract_20,
  area_id = 33,
  harga_sewa_bulanan = 16200000.0,
  on_hire_date = '2025-12-06',
  rate_changed_at = NOW()
WHERE no_unit = 5753;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_10,
  kontrak_id = @contract_30,
  area_id = 19,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2322;
UPDATE inventory_unit SET
  customer_id = 162,
  customer_location_id = 404,
  kontrak_id = @contract_31,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2323;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 8400000.0,
  on_hire_date = '2025-02-26',
  rate_changed_at = NOW()
WHERE no_unit = 5346;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_11,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2025-08-28',
  rate_changed_at = NOW()
WHERE no_unit = 5754;
UPDATE inventory_unit SET
  customer_id = 27,
  customer_location_id = 86,
  kontrak_id = @contract_32,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2024-12-18',
  rate_changed_at = NOW()
WHERE no_unit = 5347;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_11,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2025-08-28',
  rate_changed_at = NOW()
WHERE no_unit = 5755;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 244,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 13900000.0,
  on_hire_date = '2023-11-24',
  rate_changed_at = NOW()
WHERE no_unit = 2342;
UPDATE inventory_unit SET
  customer_id = 9,
  customer_location_id = 62,
  kontrak_id = @contract_34,
  area_id = 2,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = '2024-09-03',
  rate_changed_at = NOW()
WHERE no_unit = 5350;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 421,
  kontrak_id = @contract_35,
  area_id = NULL,
  harga_sewa_bulanan = 15500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2346;
UPDATE inventory_unit SET
  customer_id = 113,
  customer_location_id = 317,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-10-05',
  rate_changed_at = NOW()
WHERE no_unit = 5351;
UPDATE inventory_unit SET
  customer_id = 145,
  customer_location_id = 383,
  kontrak_id = @contract_26,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2023-08-02',
  rate_changed_at = NOW()
WHERE no_unit = 2351;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_11,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-08-29',
  rate_changed_at = NOW()
WHERE no_unit = 5761;
UPDATE inventory_unit SET
  customer_id = 11,
  customer_location_id = @location_12,
  kontrak_id = @contract_36,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2365;
UPDATE inventory_unit SET
  customer_id = 133,
  customer_location_id = 352,
  kontrak_id = @contract_37,
  area_id = 12,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = '2026-01-02',
  rate_changed_at = NOW()
WHERE no_unit = 5355;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 113,
  kontrak_id = @contract_38,
  area_id = NULL,
  harga_sewa_bulanan = 10050000.0,
  on_hire_date = '2025-08-26',
  rate_changed_at = NOW()
WHERE no_unit = 5768;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 113,
  kontrak_id = @contract_39,
  area_id = NULL,
  harga_sewa_bulanan = 10050000.0,
  on_hire_date = '2025-08-26',
  rate_changed_at = NOW()
WHERE no_unit = 5769;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 264,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 15300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2405;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 113,
  kontrak_id = @contract_40,
  area_id = NULL,
  harga_sewa_bulanan = 10050000.0,
  on_hire_date = '2025-08-26',
  rate_changed_at = NOW()
WHERE no_unit = 5770;
UPDATE inventory_unit SET
  customer_id = 30,
  customer_location_id = 98,
  kontrak_id = @contract_41,
  area_id = NULL,
  harga_sewa_bulanan = 9200000.0,
  on_hire_date = '2025-12-05',
  rate_changed_at = NOW()
WHERE no_unit = 5771;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-09-26',
  rate_changed_at = NOW()
WHERE no_unit = 5365;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 114,
  kontrak_id = @contract_42,
  area_id = NULL,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2025-08-21',
  rate_changed_at = NOW()
WHERE no_unit = 5772;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 113,
  kontrak_id = @contract_43,
  area_id = NULL,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2025-08-21',
  rate_changed_at = NOW()
WHERE no_unit = 5773;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 112,
  kontrak_id = @contract_44,
  area_id = NULL,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2025-08-26',
  rate_changed_at = NOW()
WHERE no_unit = 5774;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 114,
  kontrak_id = @contract_45,
  area_id = NULL,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2025-08-26',
  rate_changed_at = NOW()
WHERE no_unit = 5775;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 112,
  kontrak_id = @contract_46,
  area_id = NULL,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2025-08-26',
  rate_changed_at = NOW()
WHERE no_unit = 5776;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 249,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-09-26',
  rate_changed_at = NOW()
WHERE no_unit = 5370;
UPDATE inventory_unit SET
  customer_id = 155,
  customer_location_id = 396,
  kontrak_id = @contract_47,
  area_id = 19,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2025-10-27',
  rate_changed_at = NOW()
WHERE no_unit = 2461;
UPDATE inventory_unit SET
  customer_id = 230,
  customer_location_id = @location_13,
  kontrak_id = @contract_48,
  area_id = 19,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = '2026-01-08',
  rate_changed_at = NOW()
WHERE no_unit = 2466;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_14,
  kontrak_id = @contract_49,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2021-07-28',
  rate_changed_at = NOW()
WHERE no_unit = 2468;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_50,
  area_id = 35,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = '2025-12-10',
  rate_changed_at = NOW()
WHERE no_unit = 2488;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_50,
  area_id = 35,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = '2025-12-10',
  rate_changed_at = NOW()
WHERE no_unit = 2499;
UPDATE inventory_unit SET
  customer_id = 122,
  customer_location_id = 329,
  kontrak_id = @contract_51,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = '2025-12-06',
  rate_changed_at = NOW()
WHERE no_unit = 2507;
UPDATE inventory_unit SET
  customer_id = 9,
  customer_location_id = 62,
  kontrak_id = @contract_52,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = '2023-05-13',
  rate_changed_at = NOW()
WHERE no_unit = 2539;
UPDATE inventory_unit SET
  customer_id = 13,
  customer_location_id = @location_15,
  kontrak_id = @contract_53,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2542;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 53,
  kontrak_id = @contract_19,
  area_id = NULL,
  harga_sewa_bulanan = 7150000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2547;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 49,
  kontrak_id = @contract_19,
  area_id = NULL,
  harga_sewa_bulanan = 7150000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2549;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 48,
  kontrak_id = @contract_19,
  area_id = NULL,
  harga_sewa_bulanan = 7150000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2550;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 2552;

-- Processed 100/2178 units

UPDATE inventory_unit SET
  customer_id = 80,
  customer_location_id = @location_17,
  kontrak_id = @contract_54,
  area_id = NULL,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = '2023-02-15',
  rate_changed_at = NOW()
WHERE no_unit = 2564;
UPDATE inventory_unit SET
  customer_id = 166,
  customer_location_id = 408,
  kontrak_id = @contract_15,
  area_id = NULL,
  harga_sewa_bulanan = 6750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2586;
UPDATE inventory_unit SET
  customer_id = 153,
  customer_location_id = @location_18,
  kontrak_id = @contract_55,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = '2025-08-22',
  rate_changed_at = NOW()
WHERE no_unit = 540;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = @contract_56,
  area_id = NULL,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5807;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 264,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11600000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2594;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 264,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11150000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2595;
UPDATE inventory_unit SET
  customer_id = 30,
  customer_location_id = 96,
  kontrak_id = @contract_57,
  area_id = NULL,
  harga_sewa_bulanan = 16200000.0,
  on_hire_date = '2025-09-03',
  rate_changed_at = NOW()
WHERE no_unit = 5808;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = @contract_56,
  area_id = NULL,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5810;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 47,
  kontrak_id = @contract_19,
  area_id = NULL,
  harga_sewa_bulanan = 7150000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2611;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 47,
  kontrak_id = @contract_19,
  area_id = NULL,
  harga_sewa_bulanan = 7150000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2612;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 50,
  kontrak_id = @contract_19,
  area_id = NULL,
  harga_sewa_bulanan = 7150000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2613;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 51,
  kontrak_id = @contract_19,
  area_id = NULL,
  harga_sewa_bulanan = 7150000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2614;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = @contract_56,
  area_id = NULL,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5811;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = @location_19,
  kontrak_id = @contract_58,
  area_id = 20,
  harga_sewa_bulanan = 48500000.0,
  on_hire_date = '2023-06-27',
  rate_changed_at = NOW()
WHERE no_unit = 2621;
UPDATE inventory_unit SET
  customer_id = 122,
  customer_location_id = 329,
  kontrak_id = @contract_59,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2025-12-18',
  rate_changed_at = NOW()
WHERE no_unit = 2634;
UPDATE inventory_unit SET
  customer_id = 11,
  customer_location_id = @location_12,
  kontrak_id = @contract_60,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2638;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = @contract_61,
  area_id = 18,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-05-16',
  rate_changed_at = NOW()
WHERE no_unit = 2639;
UPDATE inventory_unit SET
  customer_id = 29,
  customer_location_id = 92,
  kontrak_id = @contract_62,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2642;
UPDATE inventory_unit SET
  customer_id = 99,
  customer_location_id = @location_20,
  kontrak_id = @contract_63,
  area_id = NULL,
  harga_sewa_bulanan = 7800000.0,
  on_hire_date = '2025-11-21',
  rate_changed_at = NOW()
WHERE no_unit = 2645;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 53,
  kontrak_id = @contract_19,
  area_id = NULL,
  harga_sewa_bulanan = 8750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2651;
UPDATE inventory_unit SET
  customer_id = 72,
  customer_location_id = 218,
  kontrak_id = @contract_64,
  area_id = NULL,
  harga_sewa_bulanan = 2000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2657;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 264,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 12800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2671;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = @location_21,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2672;
UPDATE inventory_unit SET
  customer_id = 229,
  customer_location_id = @location_22,
  kontrak_id = @contract_35,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2684;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = @location_21,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 9300000.0,
  on_hire_date = '2022-01-07',
  rate_changed_at = NOW()
WHERE no_unit = 638;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_23,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2024-07-20',
  rate_changed_at = NOW()
WHERE no_unit = 2686;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_24,
  kontrak_id = @contract_65,
  area_id = 17,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = '2024-10-04',
  rate_changed_at = NOW()
WHERE no_unit = 2687;
UPDATE inventory_unit SET
  customer_id = 178,
  customer_location_id = 423,
  kontrak_id = @contract_66,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2694;
UPDATE inventory_unit SET
  customer_id = 25,
  customer_location_id = 84,
  kontrak_id = @contract_67,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2696;
UPDATE inventory_unit SET
  customer_id = 82,
  customer_location_id = 234,
  kontrak_id = NULL,
  area_id = 33,
  harga_sewa_bulanan = 5500000.0,
  on_hire_date = '2025-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 5422;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 264,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 12300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2719;
UPDATE inventory_unit SET
  customer_id = 30,
  customer_location_id = 95,
  kontrak_id = @contract_68,
  area_id = NULL,
  harga_sewa_bulanan = 33150000.0,
  on_hire_date = '2025-11-06',
  rate_changed_at = NOW()
WHERE no_unit = 2721;
UPDATE inventory_unit SET
  customer_id = 24,
  customer_location_id = @location_7,
  kontrak_id = @contract_69,
  area_id = NULL,
  harga_sewa_bulanan = 30500000.0,
  on_hire_date = '2025-07-26',
  rate_changed_at = NOW()
WHERE no_unit = 2722;
UPDATE inventory_unit SET
  customer_id = 184,
  customer_location_id = 429,
  kontrak_id = @contract_70,
  area_id = NULL,
  harga_sewa_bulanan = 10800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2728;
UPDATE inventory_unit SET
  customer_id = 194,
  customer_location_id = 440,
  kontrak_id = @contract_71,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2730;
UPDATE inventory_unit SET
  customer_id = 188,
  customer_location_id = 433,
  kontrak_id = @contract_72,
  area_id = 13,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2733;
UPDATE inventory_unit SET
  customer_id = 79,
  customer_location_id = 228,
  kontrak_id = @contract_73,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2025-03-11',
  rate_changed_at = NOW()
WHERE no_unit = 5563;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_10,
  kontrak_id = @contract_74,
  area_id = 19,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2737;
UPDATE inventory_unit SET
  customer_id = 29,
  customer_location_id = 92,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2739;
UPDATE inventory_unit SET
  customer_id = 7,
  customer_location_id = @location_25,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 35000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2740;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 265,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 10900000.0,
  on_hire_date = '2025-09-01',
  rate_changed_at = NOW()
WHERE no_unit = 5429;
UPDATE inventory_unit SET
  customer_id = 7,
  customer_location_id = @location_25,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 34000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2742;
UPDATE inventory_unit SET
  customer_id = 152,
  customer_location_id = 392,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 8800000.0,
  on_hire_date = '2025-02-28',
  rate_changed_at = NOW()
WHERE no_unit = 5430;
UPDATE inventory_unit SET
  customer_id = 152,
  customer_location_id = 392,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 8800000.0,
  on_hire_date = '2025-02-28',
  rate_changed_at = NOW()
WHERE no_unit = 5431;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = @contract_12,
  area_id = NULL,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-08-07',
  rate_changed_at = NOW()
WHERE no_unit = 2754;
UPDATE inventory_unit SET
  customer_id = 21,
  customer_location_id = @location_26,
  kontrak_id = @contract_75,
  area_id = NULL,
  harga_sewa_bulanan = 21666667.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2755;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 123,
  kontrak_id = @contract_3,
  area_id = 17,
  harga_sewa_bulanan = 7700.0,
  on_hire_date = '2025-07-29',
  rate_changed_at = NOW()
WHERE no_unit = 5433;
UPDATE inventory_unit SET
  customer_id = 79,
  customer_location_id = 228,
  kontrak_id = @contract_73,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2025-03-11',
  rate_changed_at = NOW()
WHERE no_unit = 5564;
UPDATE inventory_unit SET
  customer_id = 118,
  customer_location_id = 322,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2022-01-25',
  rate_changed_at = NOW()
WHERE no_unit = 2762;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 47,
  kontrak_id = @contract_19,
  area_id = NULL,
  harga_sewa_bulanan = 7150000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2766;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_14,
  kontrak_id = @contract_49,
  area_id = 19,
  harga_sewa_bulanan = 18000000.0,
  on_hire_date = '2021-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 2771;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 123,
  kontrak_id = @contract_3,
  area_id = 17,
  harga_sewa_bulanan = 7700.0,
  on_hire_date = '2025-07-29',
  rate_changed_at = NOW()
WHERE no_unit = 5436;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_14,
  kontrak_id = @contract_49,
  area_id = 19,
  harga_sewa_bulanan = 18000000.0,
  on_hire_date = '2021-08-23',
  rate_changed_at = NOW()
WHERE no_unit = 2777;
UPDATE inventory_unit SET
  customer_id = 67,
  customer_location_id = 210,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2781;
UPDATE inventory_unit SET
  customer_id = 37,
  customer_location_id = 110,
  kontrak_id = @contract_76,
  area_id = 19,
  harga_sewa_bulanan = 9100000.0,
  on_hire_date = '2021-06-28',
  rate_changed_at = NOW()
WHERE no_unit = 2784;
UPDATE inventory_unit SET
  customer_id = 7,
  customer_location_id = @location_25,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 50000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2785;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_11,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2025-09-05',
  rate_changed_at = NOW()
WHERE no_unit = 5438;
UPDATE inventory_unit SET
  customer_id = 79,
  customer_location_id = 228,
  kontrak_id = @contract_73,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2025-03-11',
  rate_changed_at = NOW()
WHERE no_unit = 5565;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 376,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 3250000.0,
  on_hire_date = '2021-08-20',
  rate_changed_at = NOW()
WHERE no_unit = 2798;
UPDATE inventory_unit SET
  customer_id = 122,
  customer_location_id = 331,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = 13500000.0,
  on_hire_date = '2025-04-22',
  rate_changed_at = NOW()
WHERE no_unit = 2801;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 376,
  kontrak_id = @contract_77,
  area_id = NULL,
  harga_sewa_bulanan = 3250000.0,
  on_hire_date = '2021-08-12',
  rate_changed_at = NOW()
WHERE no_unit = 2805;
UPDATE inventory_unit SET
  customer_id = 79,
  customer_location_id = 228,
  kontrak_id = @contract_73,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2025-03-11',
  rate_changed_at = NOW()
WHERE no_unit = 5566;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 48,
  kontrak_id = @contract_19,
  area_id = NULL,
  harga_sewa_bulanan = 10400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2827;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 175,
  kontrak_id = @contract_78,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2828;
UPDATE inventory_unit SET
  customer_id = 9,
  customer_location_id = 62,
  kontrak_id = @contract_79,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = '2024-01-09',
  rate_changed_at = NOW()
WHERE no_unit = 2834;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 419,
  kontrak_id = @contract_35,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2840;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 419,
  kontrak_id = @contract_35,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2841;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 419,
  kontrak_id = @contract_35,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2843;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_14,
  kontrak_id = @contract_49,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2021-08-25',
  rate_changed_at = NOW()
WHERE no_unit = 2854;
UPDATE inventory_unit SET
  customer_id = 35,
  customer_location_id = @location_27,
  kontrak_id = @contract_15,
  area_id = NULL,
  harga_sewa_bulanan = 9100000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 807;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_14,
  kontrak_id = @contract_49,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2021-08-25',
  rate_changed_at = NOW()
WHERE no_unit = 2855;
UPDATE inventory_unit SET
  customer_id = 14,
  customer_location_id = 68,
  kontrak_id = @contract_80,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2856;
UPDATE inventory_unit SET
  customer_id = 129,
  customer_location_id = 341,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = '2021-12-23',
  rate_changed_at = NOW()
WHERE no_unit = 810;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 264,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 6600000.0,
  on_hire_date = '2021-09-02',
  rate_changed_at = NOW()
WHERE no_unit = 2864;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_81,
  area_id = 26,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-05-15',
  rate_changed_at = NOW()
WHERE no_unit = 2869;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 378,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 822;
UPDATE inventory_unit SET
  customer_id = 184,
  customer_location_id = 429,
  kontrak_id = @contract_70,
  area_id = NULL,
  harga_sewa_bulanan = 10800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2870;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 381,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 9300000.0,
  on_hire_date = '2021-11-08',
  rate_changed_at = NOW()
WHERE no_unit = 826;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 376,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 3250000.0,
  on_hire_date = '2021-08-09',
  rate_changed_at = NOW()
WHERE no_unit = 827;
UPDATE inventory_unit SET
  customer_id = 9,
  customer_location_id = 62,
  kontrak_id = @contract_82,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2023-01-16',
  rate_changed_at = NOW()
WHERE no_unit = 2878;
UPDATE inventory_unit SET
  customer_id = 177,
  customer_location_id = 422,
  kontrak_id = @contract_9,
  area_id = NULL,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2881;
UPDATE inventory_unit SET
  customer_id = 73,
  customer_location_id = 220,
  kontrak_id = @contract_83,
  area_id = NULL,
  harga_sewa_bulanan = 10700000.0,
  on_hire_date = '2021-10-02',
  rate_changed_at = NOW()
WHERE no_unit = 2889;
UPDATE inventory_unit SET
  customer_id = 73,
  customer_location_id = 220,
  kontrak_id = @contract_83,
  area_id = NULL,
  harga_sewa_bulanan = 9700000.0,
  on_hire_date = '2021-04-21',
  rate_changed_at = NOW()
WHERE no_unit = 2890;
UPDATE inventory_unit SET
  customer_id = 73,
  customer_location_id = 220,
  kontrak_id = @contract_83,
  area_id = NULL,
  harga_sewa_bulanan = 9700000.0,
  on_hire_date = '2021-02-22',
  rate_changed_at = NOW()
WHERE no_unit = 2891;
UPDATE inventory_unit SET
  customer_id = 73,
  customer_location_id = 220,
  kontrak_id = @contract_83,
  area_id = NULL,
  harga_sewa_bulanan = 14700000.0,
  on_hire_date = '2021-04-19',
  rate_changed_at = NOW()
WHERE no_unit = 2892;
UPDATE inventory_unit SET
  customer_id = 24,
  customer_location_id = @location_2,
  kontrak_id = @contract_18,
  area_id = 33,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = '2025-08-11',
  rate_changed_at = NOW()
WHERE no_unit = 5459;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_23,
  area_id = 26,
  harga_sewa_bulanan = 22500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5460;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 379,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2024-04-19',
  rate_changed_at = NOW()
WHERE no_unit = 2897;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 379,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2898;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 266,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11400000.0,
  on_hire_date = '2021-10-15',
  rate_changed_at = NOW()
WHERE no_unit = 2899;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 266,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 10800000.0,
  on_hire_date = '2022-01-31',
  rate_changed_at = NOW()
WHERE no_unit = 2900;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 266,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 10800000.0,
  on_hire_date = '2022-01-31',
  rate_changed_at = NOW()
WHERE no_unit = 2901;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = @contract_84,
  area_id = NULL,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2023-06-23',
  rate_changed_at = NOW()
WHERE no_unit = 2902;
UPDATE inventory_unit SET
  customer_id = 30,
  customer_location_id = 94,
  kontrak_id = @contract_85,
  area_id = 19,
  harga_sewa_bulanan = 10700000.0,
  on_hire_date = '2024-12-03',
  rate_changed_at = NOW()
WHERE no_unit = 5461;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 240,
  kontrak_id = @contract_86,
  area_id = NULL,
  harga_sewa_bulanan = 19000000.0,
  on_hire_date = '2023-01-06',
  rate_changed_at = NOW()
WHERE no_unit = 2904;
UPDATE inventory_unit SET
  customer_id = 64,
  customer_location_id = @location_6,
  kontrak_id = @contract_87,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-12-13',
  rate_changed_at = NOW()
WHERE no_unit = 5463;
UPDATE inventory_unit SET
  customer_id = 64,
  customer_location_id = @location_6,
  kontrak_id = @contract_87,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-12-13',
  rate_changed_at = NOW()
WHERE no_unit = 5464;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = @location_28,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 10800000.0,
  on_hire_date = '2021-12-21',
  rate_changed_at = NOW()
WHERE no_unit = 2931;
UPDATE inventory_unit SET
  customer_id = 216,
  customer_location_id = @location_29,
  kontrak_id = @contract_88,
  area_id = 19,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2952;
UPDATE inventory_unit SET
  customer_id = 80,
  customer_location_id = @location_17,
  kontrak_id = @contract_89,
  area_id = NULL,
  harga_sewa_bulanan = 28000000.0,
  on_hire_date = '2025-11-13',
  rate_changed_at = NOW()
WHERE no_unit = 2953;

-- Processed 200/2178 units

UPDATE inventory_unit SET
  customer_id = 170,
  customer_location_id = @location_30,
  kontrak_id = @contract_90,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5000;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 5000000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 5001;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_91,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5010;
UPDATE inventory_unit SET
  customer_id = 44,
  customer_location_id = 122,
  kontrak_id = @contract_92,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2025-01-20',
  rate_changed_at = NOW()
WHERE no_unit = 5012;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 381,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 9600000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2967;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 175,
  kontrak_id = @contract_78,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2968;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 175,
  kontrak_id = @contract_78,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2969;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 175,
  kontrak_id = @contract_78,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2970;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 175,
  kontrak_id = @contract_78,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2971;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_31,
  kontrak_id = @contract_93,
  area_id = NULL,
  harga_sewa_bulanan = 26000000.0,
  on_hire_date = '2024-01-10',
  rate_changed_at = NOW()
WHERE no_unit = 5016;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_31,
  kontrak_id = @contract_93,
  area_id = NULL,
  harga_sewa_bulanan = 26000000.0,
  on_hire_date = '2024-01-15',
  rate_changed_at = NOW()
WHERE no_unit = 5017;
UPDATE inventory_unit SET
  customer_id = 14,
  customer_location_id = 70,
  kontrak_id = @contract_94,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5026;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 128,
  kontrak_id = @contract_95,
  area_id = NULL,
  harga_sewa_bulanan = 17800000.0,
  on_hire_date = '2018-11-15',
  rate_changed_at = NOW()
WHERE no_unit = 2985;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = @contract_12,
  area_id = NULL,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2023-06-26',
  rate_changed_at = NOW()
WHERE no_unit = 2986;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = '2025-02-25',
  rate_changed_at = NOW()
WHERE no_unit = 5034;
UPDATE inventory_unit SET
  customer_id = 9,
  customer_location_id = 62,
  kontrak_id = @contract_96,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2025-09-04',
  rate_changed_at = NOW()
WHERE no_unit = 2988;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5478;
UPDATE inventory_unit SET
  customer_id = 130,
  customer_location_id = 343,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2022-05-19',
  rate_changed_at = NOW()
WHERE no_unit = 2991;
UPDATE inventory_unit SET
  customer_id = 14,
  customer_location_id = 69,
  kontrak_id = @contract_98,
  area_id = NULL,
  harga_sewa_bulanan = 19000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5039;
UPDATE inventory_unit SET
  customer_id = 48,
  customer_location_id = @location_32,
  kontrak_id = @contract_99,
  area_id = NULL,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5041;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5479;
UPDATE inventory_unit SET
  customer_id = 121,
  customer_location_id = 328,
  kontrak_id = @contract_100,
  area_id = NULL,
  harga_sewa_bulanan = 11350000.0,
  on_hire_date = '2022-01-04',
  rate_changed_at = NOW()
WHERE no_unit = 2995;
UPDATE inventory_unit SET
  customer_id = 121,
  customer_location_id = 328,
  kontrak_id = @contract_100,
  area_id = NULL,
  harga_sewa_bulanan = 11350000.0,
  on_hire_date = '2021-12-30',
  rate_changed_at = NOW()
WHERE no_unit = 2996;
UPDATE inventory_unit SET
  customer_id = 121,
  customer_location_id = 328,
  kontrak_id = @contract_100,
  area_id = NULL,
  harga_sewa_bulanan = 11350000.0,
  on_hire_date = '2021-12-28',
  rate_changed_at = NOW()
WHERE no_unit = 2997;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5480;
UPDATE inventory_unit SET
  customer_id = 121,
  customer_location_id = 328,
  kontrak_id = @contract_100,
  area_id = NULL,
  harga_sewa_bulanan = 15850000.0,
  on_hire_date = '2021-12-24',
  rate_changed_at = NOW()
WHERE no_unit = 2999;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = @contract_101,
  area_id = NULL,
  harga_sewa_bulanan = 46000000.0,
  on_hire_date = '2025-01-07',
  rate_changed_at = NOW()
WHERE no_unit = 5048;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = @contract_84,
  area_id = NULL,
  harga_sewa_bulanan = 13800000.0,
  on_hire_date = '2023-06-23',
  rate_changed_at = NOW()
WHERE no_unit = 3002;
UPDATE inventory_unit SET
  customer_id = 78,
  customer_location_id = @location_33,
  kontrak_id = @contract_102,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3003;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 175,
  kontrak_id = @contract_78,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3004;
UPDATE inventory_unit SET
  customer_id = 90,
  customer_location_id = @location_34,
  kontrak_id = @contract_103,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3005;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5482;
UPDATE inventory_unit SET
  customer_id = 88,
  customer_location_id = 258,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 7350000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3007;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_31,
  kontrak_id = @contract_93,
  area_id = NULL,
  harga_sewa_bulanan = 11400000.0,
  on_hire_date = '2022-07-29',
  rate_changed_at = NOW()
WHERE no_unit = 3009;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 55,
  kontrak_id = @contract_19,
  area_id = 19,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3010;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5483;
UPDATE inventory_unit SET
  customer_id = 79,
  customer_location_id = 228,
  kontrak_id = @contract_73,
  area_id = NULL,
  harga_sewa_bulanan = 30500000.0,
  on_hire_date = '2025-05-07',
  rate_changed_at = NOW()
WHERE no_unit = 5062;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 5000000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 5064;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 5000000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 5065;
UPDATE inventory_unit SET
  customer_id = 65,
  customer_location_id = @location_35,
  kontrak_id = @contract_104,
  area_id = NULL,
  harga_sewa_bulanan = 10500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3019;
UPDATE inventory_unit SET
  customer_id = 49,
  customer_location_id = 164,
  kontrak_id = @contract_105,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2025-10-13',
  rate_changed_at = NOW()
WHERE no_unit = 3020;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 5000000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 5069;
UPDATE inventory_unit SET
  customer_id = 9,
  customer_location_id = 62,
  kontrak_id = @contract_96,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2025-09-03',
  rate_changed_at = NOW()
WHERE no_unit = 3022;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 5000000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 5071;
UPDATE inventory_unit SET
  customer_id = 9,
  customer_location_id = 62,
  kontrak_id = @contract_96,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2025-09-03',
  rate_changed_at = NOW()
WHERE no_unit = 3028;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = @location_36,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-08-02',
  rate_changed_at = NOW()
WHERE no_unit = 3029;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 5000000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 5079;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_14,
  kontrak_id = @contract_49,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2021-07-31',
  rate_changed_at = NOW()
WHERE no_unit = 3034;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 51,
  kontrak_id = @contract_19,
  area_id = NULL,
  harga_sewa_bulanan = 7150000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3035;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_14,
  kontrak_id = @contract_49,
  area_id = 19,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = '2021-07-31',
  rate_changed_at = NOW()
WHERE no_unit = 3036;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_37,
  kontrak_id = @contract_106,
  area_id = NULL,
  harga_sewa_bulanan = 5000000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 5082;
UPDATE inventory_unit SET
  customer_id = 148,
  customer_location_id = 387,
  kontrak_id = @contract_107,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = '2025-03-25',
  rate_changed_at = NOW()
WHERE no_unit = 5083;
UPDATE inventory_unit SET
  customer_id = 64,
  customer_location_id = @location_6,
  kontrak_id = @contract_108,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2025-11-27',
  rate_changed_at = NOW()
WHERE no_unit = 5085;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_11,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-10-03',
  rate_changed_at = NOW()
WHERE no_unit = 5895;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = @contract_109,
  area_id = NULL,
  harga_sewa_bulanan = 7950000.0,
  on_hire_date = '2022-05-31',
  rate_changed_at = NOW()
WHERE no_unit = 3043;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = @contract_109,
  area_id = NULL,
  harga_sewa_bulanan = 7950000.0,
  on_hire_date = '2022-05-31',
  rate_changed_at = NOW()
WHERE no_unit = 3044;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = @contract_109,
  area_id = NULL,
  harga_sewa_bulanan = 7950000.0,
  on_hire_date = '2022-05-31',
  rate_changed_at = NOW()
WHERE no_unit = 3045;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = @contract_110,
  area_id = NULL,
  harga_sewa_bulanan = 7950000.0,
  on_hire_date = '2022-05-31',
  rate_changed_at = NOW()
WHERE no_unit = 3046;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = @contract_109,
  area_id = NULL,
  harga_sewa_bulanan = 9200000.0,
  on_hire_date = '2022-05-31',
  rate_changed_at = NOW()
WHERE no_unit = 3047;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = @contract_109,
  area_id = NULL,
  harga_sewa_bulanan = 9200000.0,
  on_hire_date = '2022-05-31',
  rate_changed_at = NOW()
WHERE no_unit = 3048;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = @contract_111,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = '2025-11-17',
  rate_changed_at = NOW()
WHERE no_unit = 3049;
UPDATE inventory_unit SET
  customer_id = 162,
  customer_location_id = 404,
  kontrak_id = @contract_112,
  area_id = NULL,
  harga_sewa_bulanan = 10250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1002;
UPDATE inventory_unit SET
  customer_id = 156,
  customer_location_id = 397,
  kontrak_id = @contract_113,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = '2025-12-23',
  rate_changed_at = NOW()
WHERE no_unit = 1003;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = @contract_109,
  area_id = NULL,
  harga_sewa_bulanan = 7450000.0,
  on_hire_date = '2022-06-08',
  rate_changed_at = NOW()
WHERE no_unit = 3050;
UPDATE inventory_unit SET
  customer_id = 140,
  customer_location_id = 360,
  kontrak_id = @contract_114,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2022-06-01',
  rate_changed_at = NOW()
WHERE no_unit = 3053;
UPDATE inventory_unit SET
  customer_id = 186,
  customer_location_id = 431,
  kontrak_id = @contract_115,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1006;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = @contract_109,
  area_id = NULL,
  harga_sewa_bulanan = 7450000.0,
  on_hire_date = '2022-06-08',
  rate_changed_at = NOW()
WHERE no_unit = 3051;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = @location_38,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3052;
UPDATE inventory_unit SET
  customer_id = 88,
  customer_location_id = 259,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-05-29',
  rate_changed_at = NOW()
WHERE no_unit = 3056;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 55,
  kontrak_id = @contract_19,
  area_id = 19,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3057;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 54,
  kontrak_id = @contract_19,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3059;
UPDATE inventory_unit SET
  customer_id = 174,
  customer_location_id = 416,
  kontrak_id = @contract_116,
  area_id = NULL,
  harga_sewa_bulanan = 12800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5107;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_31,
  kontrak_id = @contract_93,
  area_id = NULL,
  harga_sewa_bulanan = 11400000.0,
  on_hire_date = '2022-07-29',
  rate_changed_at = NOW()
WHERE no_unit = 3061;
UPDATE inventory_unit SET
  customer_id = 174,
  customer_location_id = 416,
  kontrak_id = @contract_116,
  area_id = NULL,
  harga_sewa_bulanan = 12800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5108;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = @contract_109,
  area_id = NULL,
  harga_sewa_bulanan = 7950000.0,
  on_hire_date = '2022-06-13',
  rate_changed_at = NOW()
WHERE no_unit = 3063;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = @contract_109,
  area_id = NULL,
  harga_sewa_bulanan = 7950000.0,
  on_hire_date = '2022-06-13',
  rate_changed_at = NOW()
WHERE no_unit = 3064;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_39,
  kontrak_id = @contract_117,
  area_id = NULL,
  harga_sewa_bulanan = 8200000.0,
  on_hire_date = '2022-07-06',
  rate_changed_at = NOW()
WHERE no_unit = 3065;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = @contract_109,
  area_id = NULL,
  harga_sewa_bulanan = 7950000.0,
  on_hire_date = '2022-06-08',
  rate_changed_at = NOW()
WHERE no_unit = 3066;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = @contract_109,
  area_id = NULL,
  harga_sewa_bulanan = 7950000.0,
  on_hire_date = '2022-06-13',
  rate_changed_at = NOW()
WHERE no_unit = 3067;
UPDATE inventory_unit SET
  customer_id = 50,
  customer_location_id = @location_40,
  kontrak_id = @contract_118,
  area_id = NULL,
  harga_sewa_bulanan = 7950000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3068;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = @contract_109,
  area_id = NULL,
  harga_sewa_bulanan = 7950000.0,
  on_hire_date = '2022-06-13',
  rate_changed_at = NOW()
WHERE no_unit = 3069;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = @contract_109,
  area_id = NULL,
  harga_sewa_bulanan = 7950000.0,
  on_hire_date = '2022-06-09',
  rate_changed_at = NOW()
WHERE no_unit = 3070;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_31,
  kontrak_id = @contract_93,
  area_id = NULL,
  harga_sewa_bulanan = 6950000.0,
  on_hire_date = '2022-07-25',
  rate_changed_at = NOW()
WHERE no_unit = 3071;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_31,
  kontrak_id = @contract_93,
  area_id = NULL,
  harga_sewa_bulanan = 6950000.0,
  on_hire_date = '2022-07-25',
  rate_changed_at = NOW()
WHERE no_unit = 3072;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = @contract_109,
  area_id = NULL,
  harga_sewa_bulanan = 7950000.0,
  on_hire_date = '2022-06-13',
  rate_changed_at = NOW()
WHERE no_unit = 3073;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_41,
  kontrak_id = @contract_119,
  area_id = NULL,
  harga_sewa_bulanan = 6950000.0,
  on_hire_date = '2022-07-25',
  rate_changed_at = NOW()
WHERE no_unit = 3074;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = @contract_109,
  area_id = NULL,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2022-06-30',
  rate_changed_at = NOW()
WHERE no_unit = 3075;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = @contract_109,
  area_id = NULL,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2022-06-30',
  rate_changed_at = NOW()
WHERE no_unit = 3076;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_39,
  kontrak_id = @contract_117,
  area_id = NULL,
  harga_sewa_bulanan = 7600000.0,
  on_hire_date = '2022-06-30',
  rate_changed_at = NOW()
WHERE no_unit = 3077;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_39,
  kontrak_id = @contract_117,
  area_id = NULL,
  harga_sewa_bulanan = 7700000.0,
  on_hire_date = '2022-07-06',
  rate_changed_at = NOW()
WHERE no_unit = 3078;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_41,
  kontrak_id = @contract_119,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = '2022-07-25',
  rate_changed_at = NOW()
WHERE no_unit = 3079;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_41,
  kontrak_id = @contract_119,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = '2022-07-25',
  rate_changed_at = NOW()
WHERE no_unit = 3080;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_41,
  kontrak_id = @contract_119,
  area_id = NULL,
  harga_sewa_bulanan = 6950000.0,
  on_hire_date = '2022-07-25',
  rate_changed_at = NOW()
WHERE no_unit = 3081;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_41,
  kontrak_id = @contract_119,
  area_id = NULL,
  harga_sewa_bulanan = 6950000.0,
  on_hire_date = '2022-07-25',
  rate_changed_at = NOW()
WHERE no_unit = 3082;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_31,
  kontrak_id = @contract_93,
  area_id = NULL,
  harga_sewa_bulanan = 8900000.0,
  on_hire_date = '2022-07-25',
  rate_changed_at = NOW()
WHERE no_unit = 3083;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 20,
  kontrak_id = @contract_120,
  area_id = 2,
  harga_sewa_bulanan = 22030000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 5123;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 18,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 22030000.0,
  on_hire_date = '2024-01-03',
  rate_changed_at = NOW()
WHERE no_unit = 5124;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 19,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 22030000.0,
  on_hire_date = '2024-01-06',
  rate_changed_at = NOW()
WHERE no_unit = 5125;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 15,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 21030000.0,
  on_hire_date = '2023-12-28',
  rate_changed_at = NOW()
WHERE no_unit = 5126;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 17,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 22030000.0,
  on_hire_date = '2023-12-21',
  rate_changed_at = NOW()
WHERE no_unit = 5127;

-- Processed 300/2178 units

UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 14,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 22030000.0,
  on_hire_date = '2023-12-30',
  rate_changed_at = NOW()
WHERE no_unit = 5128;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = @location_42,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2023-08-23',
  rate_changed_at = NOW()
WHERE no_unit = 3090;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 9,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 25780000.0,
  on_hire_date = '2025-08-12',
  rate_changed_at = NOW()
WHERE no_unit = 5130;
UPDATE inventory_unit SET
  customer_id = 80,
  customer_location_id = @location_17,
  kontrak_id = @contract_121,
  area_id = NULL,
  harga_sewa_bulanan = 57000000.0,
  on_hire_date = '2024-01-30',
  rate_changed_at = NOW()
WHERE no_unit = 5134;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 419,
  kontrak_id = @contract_35,
  area_id = NULL,
  harga_sewa_bulanan = 23500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1045;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 123,
  kontrak_id = @contract_3,
  area_id = 17,
  harga_sewa_bulanan = 7700.0,
  on_hire_date = '2025-07-30',
  rate_changed_at = NOW()
WHERE no_unit = 5136;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 237,
  kontrak_id = @contract_122,
  area_id = NULL,
  harga_sewa_bulanan = 12900000.0,
  on_hire_date = '2022-01-10',
  rate_changed_at = NOW()
WHERE no_unit = 5137;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 237,
  kontrak_id = @contract_123,
  area_id = NULL,
  harga_sewa_bulanan = 12900000.0,
  on_hire_date = '2023-07-12',
  rate_changed_at = NOW()
WHERE no_unit = 5139;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 237,
  kontrak_id = @contract_123,
  area_id = NULL,
  harga_sewa_bulanan = 12900000.0,
  on_hire_date = '2023-07-04',
  rate_changed_at = NOW()
WHERE no_unit = 5140;
UPDATE inventory_unit SET
  customer_id = 177,
  customer_location_id = 422,
  kontrak_id = @contract_9,
  area_id = 21,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3098;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 376,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 3250000.0,
  on_hire_date = '2021-08-27',
  rate_changed_at = NOW()
WHERE no_unit = 1051;
UPDATE inventory_unit SET
  customer_id = 43,
  customer_location_id = 121,
  kontrak_id = @contract_124,
  area_id = NULL,
  harga_sewa_bulanan = 11330000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1052;
UPDATE inventory_unit SET
  customer_id = 140,
  customer_location_id = 360,
  kontrak_id = @contract_114,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2022-06-01',
  rate_changed_at = NOW()
WHERE no_unit = 3099;
UPDATE inventory_unit SET
  customer_id = 140,
  customer_location_id = 360,
  kontrak_id = @contract_114,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2022-06-01',
  rate_changed_at = NOW()
WHERE no_unit = 3100;
UPDATE inventory_unit SET
  customer_id = 11,
  customer_location_id = @location_12,
  kontrak_id = @contract_36,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5142;
UPDATE inventory_unit SET
  customer_id = 72,
  customer_location_id = 218,
  kontrak_id = @contract_125,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5148;
UPDATE inventory_unit SET
  customer_id = 102,
  customer_location_id = 290,
  kontrak_id = @contract_126,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5151;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 44,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = '2023-11-16',
  rate_changed_at = NOW()
WHERE no_unit = 5153;
UPDATE inventory_unit SET
  customer_id = 140,
  customer_location_id = 360,
  kontrak_id = @contract_114,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2022-06-01',
  rate_changed_at = NOW()
WHERE no_unit = 3107;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_31,
  kontrak_id = @contract_93,
  area_id = NULL,
  harga_sewa_bulanan = 11400000.0,
  on_hire_date = '2022-07-29',
  rate_changed_at = NOW()
WHERE no_unit = 3108;
UPDATE inventory_unit SET
  customer_id = 7,
  customer_location_id = @location_25,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 50000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3109;
UPDATE inventory_unit SET
  customer_id = 115,
  customer_location_id = 319,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 13500000.0,
  on_hire_date = '2024-04-30',
  rate_changed_at = NOW()
WHERE no_unit = 5155;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-06-07',
  rate_changed_at = NOW()
WHERE no_unit = 5157;
UPDATE inventory_unit SET
  customer_id = 168,
  customer_location_id = 410,
  kontrak_id = @contract_127,
  area_id = NULL,
  harga_sewa_bulanan = 8750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3112;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_31,
  kontrak_id = @contract_93,
  area_id = NULL,
  harga_sewa_bulanan = 10900000.0,
  on_hire_date = '2022-07-29',
  rate_changed_at = NOW()
WHERE no_unit = 3113;
UPDATE inventory_unit SET
  customer_id = 43,
  customer_location_id = 121,
  kontrak_id = @contract_124,
  area_id = NULL,
  harga_sewa_bulanan = 9900000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3114;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = @location_43,
  kontrak_id = @contract_128,
  area_id = NULL,
  harga_sewa_bulanan = 13500000.0,
  on_hire_date = '2023-01-19',
  rate_changed_at = NOW()
WHERE no_unit = 3115;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = @location_44,
  kontrak_id = @contract_129,
  area_id = NULL,
  harga_sewa_bulanan = 13500000.0,
  on_hire_date = '2023-01-19',
  rate_changed_at = NOW()
WHERE no_unit = 3116;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = @contract_130,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3117;
UPDATE inventory_unit SET
  customer_id = 43,
  customer_location_id = 121,
  kontrak_id = @contract_124,
  area_id = NULL,
  harga_sewa_bulanan = 10550000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3118;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_45,
  kontrak_id = @contract_131,
  area_id = 26,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3119;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-06-07',
  rate_changed_at = NOW()
WHERE no_unit = 5159;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = @location_44,
  kontrak_id = @contract_129,
  area_id = NULL,
  harga_sewa_bulanan = 13500000.0,
  on_hire_date = '2023-01-19',
  rate_changed_at = NOW()
WHERE no_unit = 3121;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_46,
  kontrak_id = @contract_132,
  area_id = 26,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3122;
UPDATE inventory_unit SET
  customer_id = 43,
  customer_location_id = 121,
  kontrak_id = @contract_124,
  area_id = NULL,
  harga_sewa_bulanan = 9900000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3123;
UPDATE inventory_unit SET
  customer_id = 64,
  customer_location_id = @location_6,
  kontrak_id = @contract_133,
  area_id = NULL,
  harga_sewa_bulanan = 11200000.0,
  on_hire_date = '2025-10-15',
  rate_changed_at = NOW()
WHERE no_unit = 5164;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 251,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 11950000.0,
  on_hire_date = '2024-06-07',
  rate_changed_at = NOW()
WHERE no_unit = 5165;
UPDATE inventory_unit SET
  customer_id = 127,
  customer_location_id = 338,
  kontrak_id = @contract_134,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2023-07-27',
  rate_changed_at = NOW()
WHERE no_unit = 3126;
UPDATE inventory_unit SET
  customer_id = 148,
  customer_location_id = 388,
  kontrak_id = @contract_135,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = '2025-08-07',
  rate_changed_at = NOW()
WHERE no_unit = 5167;
UPDATE inventory_unit SET
  customer_id = 148,
  customer_location_id = 388,
  kontrak_id = @contract_135,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = '2025-08-06',
  rate_changed_at = NOW()
WHERE no_unit = 5172;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 419,
  kontrak_id = @contract_35,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5173;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = @location_44,
  kontrak_id = @contract_129,
  area_id = NULL,
  harga_sewa_bulanan = 13500000.0,
  on_hire_date = '2023-01-19',
  rate_changed_at = NOW()
WHERE no_unit = 3133;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_136,
  area_id = 26,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3134;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_137,
  area_id = 26,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3135;
UPDATE inventory_unit SET
  customer_id = 88,
  customer_location_id = 259,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 7350000.0,
  on_hire_date = '2022-11-26',
  rate_changed_at = NOW()
WHERE no_unit = 3136;
UPDATE inventory_unit SET
  customer_id = 88,
  customer_location_id = 258,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 7350000.0,
  on_hire_date = '2022-11-26',
  rate_changed_at = NOW()
WHERE no_unit = 3137;
UPDATE inventory_unit SET
  customer_id = 33,
  customer_location_id = 103,
  kontrak_id = @contract_138,
  area_id = NULL,
  harga_sewa_bulanan = 30000000.0,
  on_hire_date = '2025-02-14',
  rate_changed_at = NOW()
WHERE no_unit = 3138;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 419,
  kontrak_id = @contract_35,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5185;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 237,
  kontrak_id = @contract_122,
  area_id = NULL,
  harga_sewa_bulanan = 13400000.0,
  on_hire_date = '2022-01-11',
  rate_changed_at = NOW()
WHERE no_unit = 3140;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 237,
  kontrak_id = @contract_122,
  area_id = NULL,
  harga_sewa_bulanan = 13400000.0,
  on_hire_date = '2022-08-12',
  rate_changed_at = NOW()
WHERE no_unit = 3141;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = 223,
  kontrak_id = @contract_139,
  area_id = NULL,
  harga_sewa_bulanan = 12400000.0,
  on_hire_date = '2025-05-13',
  rate_changed_at = NOW()
WHERE no_unit = 5186;
UPDATE inventory_unit SET
  customer_id = 128,
  customer_location_id = 339,
  kontrak_id = @contract_6,
  area_id = 19,
  harga_sewa_bulanan = 9900000.0,
  on_hire_date = '2025-01-22',
  rate_changed_at = NOW()
WHERE no_unit = 5187;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_31,
  kontrak_id = @contract_93,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2022-08-06',
  rate_changed_at = NOW()
WHERE no_unit = 3144;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_31,
  kontrak_id = @contract_93,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2022-08-06',
  rate_changed_at = NOW()
WHERE no_unit = 3145;
UPDATE inventory_unit SET
  customer_id = 69,
  customer_location_id = 213,
  kontrak_id = @contract_140,
  area_id = NULL,
  harga_sewa_bulanan = 17500000.0,
  on_hire_date = '2024-07-31',
  rate_changed_at = NOW()
WHERE no_unit = 5190;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 56,
  kontrak_id = @contract_19,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3147;
UPDATE inventory_unit SET
  customer_id = 21,
  customer_location_id = @location_26,
  kontrak_id = @contract_141,
  area_id = NULL,
  harga_sewa_bulanan = 24000000.0,
  on_hire_date = '2022-03-31',
  rate_changed_at = NOW()
WHERE no_unit = 5192;
UPDATE inventory_unit SET
  customer_id = 170,
  customer_location_id = 412,
  kontrak_id = @contract_142,
  area_id = 33,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5195;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_11,
  kontrak_id = @contract_143,
  area_id = 19,
  harga_sewa_bulanan = 16000000.0,
  on_hire_date = '2024-12-03',
  rate_changed_at = NOW()
WHERE no_unit = 5196;
UPDATE inventory_unit SET
  customer_id = 170,
  customer_location_id = @location_47,
  kontrak_id = @contract_116,
  area_id = 33,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5197;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_11,
  kontrak_id = @contract_144,
  area_id = 19,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = '2024-11-11',
  rate_changed_at = NOW()
WHERE no_unit = 5198;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_10,
  kontrak_id = @contract_145,
  area_id = 19,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5199;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_10,
  kontrak_id = @contract_146,
  area_id = 19,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5200;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 379,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = '2024-05-08',
  rate_changed_at = NOW()
WHERE no_unit = 5203;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 379,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = '2024-06-11',
  rate_changed_at = NOW()
WHERE no_unit = 5204;
UPDATE inventory_unit SET
  customer_id = 88,
  customer_location_id = 258,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 7350000.0,
  on_hire_date = '2022-11-26',
  rate_changed_at = NOW()
WHERE no_unit = 3157;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_147,
  area_id = 26,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5205;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = 26,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-05-15',
  rate_changed_at = NOW()
WHERE no_unit = 5206;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = 26,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-11-29',
  rate_changed_at = NOW()
WHERE no_unit = 5208;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 249,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 11950000.0,
  on_hire_date = '2024-06-14',
  rate_changed_at = NOW()
WHERE no_unit = 5209;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 237,
  kontrak_id = @contract_148,
  area_id = NULL,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = '2022-09-22',
  rate_changed_at = NOW()
WHERE no_unit = 3162;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = 232,
  kontrak_id = @contract_149,
  area_id = NULL,
  harga_sewa_bulanan = 36500000.0,
  on_hire_date = '2025-12-05',
  rate_changed_at = NOW()
WHERE no_unit = 3163;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 251,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 11950000.0,
  on_hire_date = '2024-06-07',
  rate_changed_at = NOW()
WHERE no_unit = 5210;
UPDATE inventory_unit SET
  customer_id = 73,
  customer_location_id = 220,
  kontrak_id = @contract_83,
  area_id = NULL,
  harga_sewa_bulanan = 32700000.0,
  on_hire_date = '2022-07-26',
  rate_changed_at = NOW()
WHERE no_unit = 3165;
UPDATE inventory_unit SET
  customer_id = 115,
  customer_location_id = 319,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 13500000.0,
  on_hire_date = '2024-09-02',
  rate_changed_at = NOW()
WHERE no_unit = 5212;
UPDATE inventory_unit SET
  customer_id = 64,
  customer_location_id = 191,
  kontrak_id = @contract_150,
  area_id = NULL,
  harga_sewa_bulanan = 13500000.0,
  on_hire_date = '2025-04-26',
  rate_changed_at = NOW()
WHERE no_unit = 5213;
UPDATE inventory_unit SET
  customer_id = 100,
  customer_location_id = @location_48,
  kontrak_id = @contract_151,
  area_id = NULL,
  harga_sewa_bulanan = 9500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3170;
UPDATE inventory_unit SET
  customer_id = 100,
  customer_location_id = @location_48,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 9500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3171;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 264,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 8200000.0,
  on_hire_date = '2023-01-31',
  rate_changed_at = NOW()
WHERE no_unit = 3172;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 14900000.0,
  on_hire_date = '2024-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 5218;
UPDATE inventory_unit SET
  customer_id = 99,
  customer_location_id = @location_20,
  kontrak_id = @contract_152,
  area_id = NULL,
  harga_sewa_bulanan = 7800000.0,
  on_hire_date = '2025-11-21',
  rate_changed_at = NOW()
WHERE no_unit = 3174;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 14900000.0,
  on_hire_date = '2024-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 5219;
UPDATE inventory_unit SET
  customer_id = 34,
  customer_location_id = 106,
  kontrak_id = @contract_153,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3176;
UPDATE inventory_unit SET
  customer_id = 174,
  customer_location_id = 416,
  kontrak_id = @contract_116,
  area_id = NULL,
  harga_sewa_bulanan = 12800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5109;
UPDATE inventory_unit SET
  customer_id = 97,
  customer_location_id = 283,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 13500000.0,
  on_hire_date = '2024-11-12',
  rate_changed_at = NOW()
WHERE no_unit = 5220;
UPDATE inventory_unit SET
  customer_id = 80,
  customer_location_id = @location_17,
  kontrak_id = @contract_54,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = '2023-02-13',
  rate_changed_at = NOW()
WHERE no_unit = 3179;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = @location_49,
  kontrak_id = @contract_154,
  area_id = NULL,
  harga_sewa_bulanan = 38900000.0,
  on_hire_date = '2025-05-30',
  rate_changed_at = NOW()
WHERE no_unit = 3180;
UPDATE inventory_unit SET
  customer_id = 80,
  customer_location_id = @location_17,
  kontrak_id = @contract_54,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = '2023-02-14',
  rate_changed_at = NOW()
WHERE no_unit = 3181;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 266,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 9300000.0,
  on_hire_date = '2024-06-21',
  rate_changed_at = NOW()
WHERE no_unit = 5223;
UPDATE inventory_unit SET
  customer_id = 64,
  customer_location_id = @location_6,
  kontrak_id = @contract_150,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2025-04-28',
  rate_changed_at = NOW()
WHERE no_unit = 5224;
UPDATE inventory_unit SET
  customer_id = 49,
  customer_location_id = @location_50,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2024-07-03',
  rate_changed_at = NOW()
WHERE no_unit = 5225;
UPDATE inventory_unit SET
  customer_id = 115,
  customer_location_id = 319,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 13500000.0,
  on_hire_date = '2024-09-02',
  rate_changed_at = NOW()
WHERE no_unit = 5226;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 14900000.0,
  on_hire_date = '2024-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 5227;
UPDATE inventory_unit SET
  customer_id = 49,
  customer_location_id = @location_50,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = '2024-07-03',
  rate_changed_at = NOW()
WHERE no_unit = 5229;
UPDATE inventory_unit SET
  customer_id = 7,
  customer_location_id = @location_25,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 27000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3188;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 14900000.0,
  on_hire_date = '2024-11-22',
  rate_changed_at = NOW()
WHERE no_unit = 5230;
UPDATE inventory_unit SET
  customer_id = 174,
  customer_location_id = 416,
  kontrak_id = @contract_155,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5231;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = @contract_156,
  area_id = NULL,
  harga_sewa_bulanan = 24000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5583;
UPDATE inventory_unit SET
  customer_id = 174,
  customer_location_id = 416,
  kontrak_id = @contract_116,
  area_id = NULL,
  harga_sewa_bulanan = 6800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5112;
UPDATE inventory_unit SET
  customer_id = 34,
  customer_location_id = 106,
  kontrak_id = @contract_153,
  area_id = NULL,
  harga_sewa_bulanan = 16600000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3193;

-- Processed 400/2178 units

UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = @contract_157,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3194;
UPDATE inventory_unit SET
  customer_id = 37,
  customer_location_id = 110,
  kontrak_id = @contract_158,
  area_id = 19,
  harga_sewa_bulanan = 14500000.0,
  on_hire_date = '2024-07-08',
  rate_changed_at = NOW()
WHERE no_unit = 5242;
UPDATE inventory_unit SET
  customer_id = 122,
  customer_location_id = 330,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2025-04-23',
  rate_changed_at = NOW()
WHERE no_unit = 5243;
UPDATE inventory_unit SET
  customer_id = 122,
  customer_location_id = 329,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = '2024-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 3197;
UPDATE inventory_unit SET
  customer_id = 174,
  customer_location_id = 416,
  kontrak_id = @contract_116,
  area_id = NULL,
  harga_sewa_bulanan = 6800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5113;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 251,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 11950000.0,
  on_hire_date = '2024-10-22',
  rate_changed_at = NOW()
WHERE no_unit = 5244;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 251,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 11950000.0,
  on_hire_date = '2024-10-11',
  rate_changed_at = NOW()
WHERE no_unit = 5245;
UPDATE inventory_unit SET
  customer_id = 122,
  customer_location_id = 329,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2022-03-22',
  rate_changed_at = NOW()
WHERE no_unit = 5246;
UPDATE inventory_unit SET
  customer_id = 174,
  customer_location_id = 416,
  kontrak_id = @contract_116,
  area_id = NULL,
  harga_sewa_bulanan = 6800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5114;
UPDATE inventory_unit SET
  customer_id = 94,
  customer_location_id = 275,
  kontrak_id = @contract_6,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2022-03-23',
  rate_changed_at = NOW()
WHERE no_unit = 3203;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_51,
  kontrak_id = @contract_159,
  area_id = NULL,
  harga_sewa_bulanan = 27500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5248;
UPDATE inventory_unit SET
  customer_id = 174,
  customer_location_id = 416,
  kontrak_id = @contract_116,
  area_id = NULL,
  harga_sewa_bulanan = 6800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5115;
UPDATE inventory_unit SET
  customer_id = 117,
  customer_location_id = 321,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 24000000.0,
  on_hire_date = '2024-08-29',
  rate_changed_at = NOW()
WHERE no_unit = 5257;
UPDATE inventory_unit SET
  customer_id = 117,
  customer_location_id = 321,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = '2024-08-29',
  rate_changed_at = NOW()
WHERE no_unit = 5258;
UPDATE inventory_unit SET
  customer_id = 117,
  customer_location_id = 321,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2024-08-29',
  rate_changed_at = NOW()
WHERE no_unit = 5259;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_39,
  kontrak_id = @contract_117,
  area_id = NULL,
  harga_sewa_bulanan = 11900000.0,
  on_hire_date = '2022-11-02',
  rate_changed_at = NOW()
WHERE no_unit = 3212;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_39,
  kontrak_id = @contract_117,
  area_id = NULL,
  harga_sewa_bulanan = 10900000.0,
  on_hire_date = '2022-10-28',
  rate_changed_at = NOW()
WHERE no_unit = 3213;
UPDATE inventory_unit SET
  customer_id = 174,
  customer_location_id = 416,
  kontrak_id = @contract_116,
  area_id = NULL,
  harga_sewa_bulanan = 6800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5116;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 237,
  kontrak_id = @contract_122,
  area_id = NULL,
  harga_sewa_bulanan = 12900000.0,
  on_hire_date = '2022-02-10',
  rate_changed_at = NOW()
WHERE no_unit = 3215;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 237,
  kontrak_id = @contract_122,
  area_id = NULL,
  harga_sewa_bulanan = 12900000.0,
  on_hire_date = '2022-02-26',
  rate_changed_at = NOW()
WHERE no_unit = 3216;
UPDATE inventory_unit SET
  customer_id = 174,
  customer_location_id = 416,
  kontrak_id = @contract_116,
  area_id = NULL,
  harga_sewa_bulanan = 6800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5117;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 237,
  kontrak_id = @contract_122,
  area_id = NULL,
  harga_sewa_bulanan = 12900000.0,
  on_hire_date = '2022-03-05',
  rate_changed_at = NOW()
WHERE no_unit = 3218;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = @location_49,
  kontrak_id = @contract_160,
  area_id = NULL,
  harga_sewa_bulanan = 60000000.0,
  on_hire_date = '2022-12-26',
  rate_changed_at = NOW()
WHERE no_unit = 5260;
UPDATE inventory_unit SET
  customer_id = 127,
  customer_location_id = 338,
  kontrak_id = @contract_161,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2024-08-14',
  rate_changed_at = NOW()
WHERE no_unit = 5261;
UPDATE inventory_unit SET
  customer_id = 14,
  customer_location_id = 68,
  kontrak_id = @contract_162,
  area_id = NULL,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5262;
UPDATE inventory_unit SET
  customer_id = 27,
  customer_location_id = 87,
  kontrak_id = @contract_163,
  area_id = NULL,
  harga_sewa_bulanan = 31500000.0,
  on_hire_date = '2024-02-05',
  rate_changed_at = NOW()
WHERE no_unit = 5118;
UPDATE inventory_unit SET
  customer_id = 14,
  customer_location_id = @location_52,
  kontrak_id = @contract_162,
  area_id = NULL,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5263;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_10,
  kontrak_id = @contract_164,
  area_id = 19,
  harga_sewa_bulanan = 21500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5268;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_23,
  area_id = 26,
  harga_sewa_bulanan = 22500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5272;
UPDATE inventory_unit SET
  customer_id = 27,
  customer_location_id = 89,
  kontrak_id = @contract_165,
  area_id = 19,
  harga_sewa_bulanan = 17000000.0,
  on_hire_date = '2024-10-30',
  rate_changed_at = NOW()
WHERE no_unit = 5274;
UPDATE inventory_unit SET
  customer_id = 27,
  customer_location_id = 88,
  kontrak_id = @contract_166,
  area_id = NULL,
  harga_sewa_bulanan = 31500000.0,
  on_hire_date = '2024-02-05',
  rate_changed_at = NOW()
WHERE no_unit = 5119;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 379,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = '2024-09-03',
  rate_changed_at = NOW()
WHERE no_unit = 5276;
UPDATE inventory_unit SET
  customer_id = 80,
  customer_location_id = @location_17,
  kontrak_id = @contract_54,
  area_id = NULL,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = '2023-02-15',
  rate_changed_at = NOW()
WHERE no_unit = 3229;
UPDATE inventory_unit SET
  customer_id = 93,
  customer_location_id = 273,
  kontrak_id = @contract_167,
  area_id = NULL,
  harga_sewa_bulanan = 16000000.0,
  on_hire_date = '2025-11-13',
  rate_changed_at = NOW()
WHERE no_unit = 3230;
UPDATE inventory_unit SET
  customer_id = 24,
  customer_location_id = @location_2,
  kontrak_id = @contract_168,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2024-11-11',
  rate_changed_at = NOW()
WHERE no_unit = 5279;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-09-30',
  rate_changed_at = NOW()
WHERE no_unit = 5280;
UPDATE inventory_unit SET
  customer_id = 27,
  customer_location_id = 88,
  kontrak_id = @contract_166,
  area_id = NULL,
  harga_sewa_bulanan = 31500000.0,
  on_hire_date = '2024-02-05',
  rate_changed_at = NOW()
WHERE no_unit = 5120;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_53,
  kontrak_id = @contract_169,
  area_id = 19,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3234;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_53,
  kontrak_id = @contract_169,
  area_id = 19,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3235;
UPDATE inventory_unit SET
  customer_id = 24,
  customer_location_id = @location_54,
  kontrak_id = @contract_170,
  area_id = NULL,
  harga_sewa_bulanan = 22000000.0,
  on_hire_date = '2025-12-15',
  rate_changed_at = NOW()
WHERE no_unit = 5281;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_53,
  kontrak_id = @contract_169,
  area_id = 19,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3237;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_53,
  kontrak_id = @contract_169,
  area_id = 19,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3238;
UPDATE inventory_unit SET
  customer_id = 27,
  customer_location_id = 87,
  kontrak_id = @contract_163,
  area_id = NULL,
  harga_sewa_bulanan = 31500000.0,
  on_hire_date = '2024-02-05',
  rate_changed_at = NOW()
WHERE no_unit = 5121;
UPDATE inventory_unit SET
  customer_id = 30,
  customer_location_id = 100,
  kontrak_id = @contract_171,
  area_id = NULL,
  harga_sewa_bulanan = 16200000.0,
  on_hire_date = '2025-12-20',
  rate_changed_at = NOW()
WHERE no_unit = 5282;
UPDATE inventory_unit SET
  customer_id = 96,
  customer_location_id = 281,
  kontrak_id = @contract_172,
  area_id = NULL,
  harga_sewa_bulanan = 11300000.0,
  on_hire_date = '2025-03-26',
  rate_changed_at = NOW()
WHERE no_unit = 5283;
UPDATE inventory_unit SET
  customer_id = 96,
  customer_location_id = @location_55,
  kontrak_id = @contract_173,
  area_id = NULL,
  harga_sewa_bulanan = 11750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5284;
UPDATE inventory_unit SET
  customer_id = 14,
  customer_location_id = 71,
  kontrak_id = @contract_174,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3243;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = @location_56,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 22030000.0,
  on_hire_date = '2023-12-28',
  rate_changed_at = NOW()
WHERE no_unit = 5122;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = @location_57,
  kontrak_id = NULL,
  area_id = 13,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-02-08',
  rate_changed_at = NOW()
WHERE no_unit = 3245;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_50,
  area_id = 35,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = '2025-12-10',
  rate_changed_at = NOW()
WHERE no_unit = 5285;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = @location_57,
  kontrak_id = NULL,
  area_id = 13,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-02-08',
  rate_changed_at = NOW()
WHERE no_unit = 3247;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 123,
  kontrak_id = @contract_3,
  area_id = 17,
  harga_sewa_bulanan = 7700.0,
  on_hire_date = '2025-07-30',
  rate_changed_at = NOW()
WHERE no_unit = 5288;
UPDATE inventory_unit SET
  customer_id = 21,
  customer_location_id = @location_26,
  kontrak_id = @contract_175,
  area_id = 18,
  harga_sewa_bulanan = 12900000.0,
  on_hire_date = '2024-08-30',
  rate_changed_at = NOW()
WHERE no_unit = 5289;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = @location_58,
  kontrak_id = @contract_176,
  area_id = 17,
  harga_sewa_bulanan = 9250000.0,
  on_hire_date = '2024-09-07',
  rate_changed_at = NOW()
WHERE no_unit = 1202;
UPDATE inventory_unit SET
  customer_id = 122,
  customer_location_id = 330,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-11-29',
  rate_changed_at = NOW()
WHERE no_unit = 5290;
UPDATE inventory_unit SET
  customer_id = 73,
  customer_location_id = 220,
  kontrak_id = @contract_83,
  area_id = NULL,
  harga_sewa_bulanan = 32700000.0,
  on_hire_date = '2021-12-22',
  rate_changed_at = NOW()
WHERE no_unit = 3252;
UPDATE inventory_unit SET
  customer_id = 96,
  customer_location_id = @location_55,
  kontrak_id = @contract_173,
  area_id = NULL,
  harga_sewa_bulanan = 11750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5293;
UPDATE inventory_unit SET
  customer_id = 96,
  customer_location_id = @location_55,
  kontrak_id = @contract_173,
  area_id = NULL,
  harga_sewa_bulanan = 11750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5294;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = @location_59,
  kontrak_id = @contract_177,
  area_id = NULL,
  harga_sewa_bulanan = 5850000.0,
  on_hire_date = '2023-01-18',
  rate_changed_at = NOW()
WHERE no_unit = 3255;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = @location_59,
  kontrak_id = @contract_177,
  area_id = NULL,
  harga_sewa_bulanan = 5850000.0,
  on_hire_date = '2023-01-18',
  rate_changed_at = NOW()
WHERE no_unit = 3256;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = @location_44,
  kontrak_id = @contract_128,
  area_id = NULL,
  harga_sewa_bulanan = 5850000.0,
  on_hire_date = '2023-01-18',
  rate_changed_at = NOW()
WHERE no_unit = 3257;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = @location_59,
  kontrak_id = @contract_177,
  area_id = NULL,
  harga_sewa_bulanan = 5850000.0,
  on_hire_date = '2023-01-18',
  rate_changed_at = NOW()
WHERE no_unit = 3258;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = @location_44,
  kontrak_id = @contract_129,
  area_id = NULL,
  harga_sewa_bulanan = 5850000.0,
  on_hire_date = '2023-01-18',
  rate_changed_at = NOW()
WHERE no_unit = 3259;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = @location_60,
  kontrak_id = @contract_128,
  area_id = NULL,
  harga_sewa_bulanan = 5850000.0,
  on_hire_date = '2023-01-19',
  rate_changed_at = NOW()
WHERE no_unit = 3260;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = @location_60,
  kontrak_id = @contract_129,
  area_id = NULL,
  harga_sewa_bulanan = 5850000.0,
  on_hire_date = '2023-01-18',
  rate_changed_at = NOW()
WHERE no_unit = 3261;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_11,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2025-09-05',
  rate_changed_at = NOW()
WHERE no_unit = 5301;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = @location_60,
  kontrak_id = @contract_129,
  area_id = NULL,
  harga_sewa_bulanan = 5850000.0,
  on_hire_date = '2023-01-19',
  rate_changed_at = NOW()
WHERE no_unit = 3263;
UPDATE inventory_unit SET
  customer_id = 27,
  customer_location_id = 86,
  kontrak_id = @contract_32,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2024-12-04',
  rate_changed_at = NOW()
WHERE no_unit = 5305;
UPDATE inventory_unit SET
  customer_id = 214,
  customer_location_id = @location_61,
  kontrak_id = @contract_178,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2025-12-16',
  rate_changed_at = NOW()
WHERE no_unit = 5306;
UPDATE inventory_unit SET
  customer_id = 131,
  customer_location_id = 344,
  kontrak_id = @contract_176,
  area_id = 19,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2024-09-07',
  rate_changed_at = NOW()
WHERE no_unit = 5309;
UPDATE inventory_unit SET
  customer_id = 142,
  customer_location_id = 365,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3267;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = @location_21,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = '2024-09-25',
  rate_changed_at = NOW()
WHERE no_unit = 5310;
UPDATE inventory_unit SET
  customer_id = 142,
  customer_location_id = 363,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = '2023-09-13',
  rate_changed_at = NOW()
WHERE no_unit = 3269;
UPDATE inventory_unit SET
  customer_id = 149,
  customer_location_id = 389,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 10600000.0,
  on_hire_date = '2024-08-31',
  rate_changed_at = NOW()
WHERE no_unit = 5311;
UPDATE inventory_unit SET
  customer_id = 179,
  customer_location_id = 424,
  kontrak_id = @contract_179,
  area_id = NULL,
  harga_sewa_bulanan = 9500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5312;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_11,
  kontrak_id = @contract_143,
  area_id = 19,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2024-12-03',
  rate_changed_at = NOW()
WHERE no_unit = 5314;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2025-01-08',
  rate_changed_at = NOW()
WHERE no_unit = 5315;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2025-01-08',
  rate_changed_at = NOW()
WHERE no_unit = 5316;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_50,
  area_id = 35,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2025-12-10',
  rate_changed_at = NOW()
WHERE no_unit = 5318;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = @contract_180,
  area_id = 18,
  harga_sewa_bulanan = 5950000.0,
  on_hire_date = '2025-02-07',
  rate_changed_at = NOW()
WHERE no_unit = 5321;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = @contract_181,
  area_id = 18,
  harga_sewa_bulanan = 5950000.0,
  on_hire_date = '2025-01-07',
  rate_changed_at = NOW()
WHERE no_unit = 5322;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 16,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 22030000.0,
  on_hire_date = '2023-12-30',
  rate_changed_at = NOW()
WHERE no_unit = 5129;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_11,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2025-09-05',
  rate_changed_at = NOW()
WHERE no_unit = 5324;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 123,
  kontrak_id = @contract_3,
  area_id = 17,
  harga_sewa_bulanan = 7700.0,
  on_hire_date = '2025-07-29',
  rate_changed_at = NOW()
WHERE no_unit = 5325;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 123,
  kontrak_id = @contract_3,
  area_id = 17,
  harga_sewa_bulanan = 7700.0,
  on_hire_date = '2025-07-29',
  rate_changed_at = NOW()
WHERE no_unit = 5327;
UPDATE inventory_unit SET
  customer_id = 117,
  customer_location_id = 321,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 87000000.0,
  on_hire_date = '2024-12-03',
  rate_changed_at = NOW()
WHERE no_unit = 5328;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 247,
  kontrak_id = @contract_65,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = '2024-10-04',
  rate_changed_at = NOW()
WHERE no_unit = 5330;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 5331;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 5332;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 5333;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 5334;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_182,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-02-02',
  rate_changed_at = NOW()
WHERE no_unit = 3288;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 5335;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 5336;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 5337;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 5338;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 5339;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 266,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3294;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = @contract_12,
  area_id = NULL,
  harga_sewa_bulanan = 5500000.0,
  on_hire_date = '2023-05-31',
  rate_changed_at = NOW()
WHERE no_unit = 3295;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_183,
  area_id = 26,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3296;

-- Processed 500/2178 units

UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 419,
  kontrak_id = @contract_35,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3297;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 420,
  kontrak_id = @contract_35,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3298;
UPDATE inventory_unit SET
  customer_id = 121,
  customer_location_id = 328,
  kontrak_id = @contract_100,
  area_id = NULL,
  harga_sewa_bulanan = 11350000.0,
  on_hire_date = '2022-09-07',
  rate_changed_at = NOW()
WHERE no_unit = 3299;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 5340;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = @location_44,
  kontrak_id = @contract_128,
  area_id = NULL,
  harga_sewa_bulanan = 27000000.0,
  on_hire_date = '2024-09-27',
  rate_changed_at = NOW()
WHERE no_unit = 5341;
UPDATE inventory_unit SET
  customer_id = 27,
  customer_location_id = 89,
  kontrak_id = @contract_184,
  area_id = 19,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = '2024-09-27',
  rate_changed_at = NOW()
WHERE no_unit = 5342;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_62,
  kontrak_id = @contract_185,
  area_id = 33,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3303;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 244,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = '2023-11-24',
  rate_changed_at = NOW()
WHERE no_unit = 3304;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 381,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1257;
UPDATE inventory_unit SET
  customer_id = 128,
  customer_location_id = @location_63,
  kontrak_id = @contract_6,
  area_id = 19,
  harga_sewa_bulanan = 9900000.0,
  on_hire_date = '2025-03-25',
  rate_changed_at = NOW()
WHERE no_unit = 5345;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 378,
  kontrak_id = @contract_186,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1259;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_62,
  kontrak_id = @contract_185,
  area_id = 33,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3308;
UPDATE inventory_unit SET
  customer_id = 27,
  customer_location_id = 87,
  kontrak_id = @contract_187,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-01-07',
  rate_changed_at = NOW()
WHERE no_unit = 5348;
UPDATE inventory_unit SET
  customer_id = 27,
  customer_location_id = 87,
  kontrak_id = @contract_187,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-01-17',
  rate_changed_at = NOW()
WHERE no_unit = 5349;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 420,
  kontrak_id = @contract_35,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3311;
UPDATE inventory_unit SET
  customer_id = 142,
  customer_location_id = 365,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = '2025-03-20',
  rate_changed_at = NOW()
WHERE no_unit = 3312;
UPDATE inventory_unit SET
  customer_id = 93,
  customer_location_id = 273,
  kontrak_id = @contract_188,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = '2025-12-04',
  rate_changed_at = NOW()
WHERE no_unit = 5352;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_10,
  kontrak_id = @contract_189,
  area_id = 19,
  harga_sewa_bulanan = 21500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3314;
UPDATE inventory_unit SET
  customer_id = 93,
  customer_location_id = 273,
  kontrak_id = @contract_188,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = '2025-12-04',
  rate_changed_at = NOW()
WHERE no_unit = 5354;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_10,
  kontrak_id = @contract_190,
  area_id = 19,
  harga_sewa_bulanan = 22500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3316;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 379,
  kontrak_id = @contract_191,
  area_id = 19,
  harga_sewa_bulanan = 10500000.0,
  on_hire_date = '2023-09-08',
  rate_changed_at = NOW()
WHERE no_unit = 3317;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-09-27',
  rate_changed_at = NOW()
WHERE no_unit = 5362;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_192,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3319;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_193,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-02-02',
  rate_changed_at = NOW()
WHERE no_unit = 3320;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_182,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-02-02',
  rate_changed_at = NOW()
WHERE no_unit = 3321;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_182,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-02-02',
  rate_changed_at = NOW()
WHERE no_unit = 3322;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-09-26',
  rate_changed_at = NOW()
WHERE no_unit = 5363;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-09-26',
  rate_changed_at = NOW()
WHERE no_unit = 5364;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_182,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-02-02',
  rate_changed_at = NOW()
WHERE no_unit = 3325;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_182,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-02-02',
  rate_changed_at = NOW()
WHERE no_unit = 3326;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-09-27',
  rate_changed_at = NOW()
WHERE no_unit = 5366;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-09-27',
  rate_changed_at = NOW()
WHERE no_unit = 5367;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-09-26',
  rate_changed_at = NOW()
WHERE no_unit = 5368;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 251,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 11950000.0,
  on_hire_date = '2024-09-27',
  rate_changed_at = NOW()
WHERE no_unit = 5369;
UPDATE inventory_unit SET
  customer_id = 80,
  customer_location_id = @location_17,
  kontrak_id = @contract_54,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = '2023-02-14',
  rate_changed_at = NOW()
WHERE no_unit = 3331;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 249,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-09-26',
  rate_changed_at = NOW()
WHERE no_unit = 5371;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = @location_64,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2024-09-28',
  rate_changed_at = NOW()
WHERE no_unit = 5372;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 249,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 11950000.0,
  on_hire_date = '2024-09-28',
  rate_changed_at = NOW()
WHERE no_unit = 5373;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 264,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = '2025-02-28',
  rate_changed_at = NOW()
WHERE no_unit = 5376;
UPDATE inventory_unit SET
  customer_id = 184,
  customer_location_id = 429,
  kontrak_id = @contract_70,
  area_id = NULL,
  harga_sewa_bulanan = 10500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5379;
UPDATE inventory_unit SET
  customer_id = 64,
  customer_location_id = @location_6,
  kontrak_id = @contract_150,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = '2025-04-26',
  rate_changed_at = NOW()
WHERE no_unit = 5380;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 237,
  kontrak_id = @contract_122,
  area_id = NULL,
  harga_sewa_bulanan = 12900000.0,
  on_hire_date = '2022-01-17',
  rate_changed_at = NOW()
WHERE no_unit = 5141;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2025-02-25',
  rate_changed_at = NOW()
WHERE no_unit = 5381;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2025-02-25',
  rate_changed_at = NOW()
WHERE no_unit = 5383;
UPDATE inventory_unit SET
  customer_id = 151,
  customer_location_id = 391,
  kontrak_id = @contract_194,
  area_id = NULL,
  harga_sewa_bulanan = 9500000.0,
  on_hire_date = '2024-10-31',
  rate_changed_at = NOW()
WHERE no_unit = 5384;
UPDATE inventory_unit SET
  customer_id = 97,
  customer_location_id = @location_65,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-11-28',
  rate_changed_at = NOW()
WHERE no_unit = 5386;
UPDATE inventory_unit SET
  customer_id = 69,
  customer_location_id = 213,
  kontrak_id = @contract_195,
  area_id = NULL,
  harga_sewa_bulanan = 17500000.0,
  on_hire_date = '2024-08-30',
  rate_changed_at = NOW()
WHERE no_unit = 5391;
UPDATE inventory_unit SET
  customer_id = 151,
  customer_location_id = 391,
  kontrak_id = @contract_196,
  area_id = NULL,
  harga_sewa_bulanan = 9500000.0,
  on_hire_date = '2025-01-31',
  rate_changed_at = NOW()
WHERE no_unit = 5549;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = @contract_197,
  area_id = NULL,
  harga_sewa_bulanan = 32500000.0,
  on_hire_date = '2024-10-22',
  rate_changed_at = NOW()
WHERE no_unit = 5393;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = @contract_197,
  area_id = NULL,
  harga_sewa_bulanan = 32500000.0,
  on_hire_date = '2024-10-22',
  rate_changed_at = NOW()
WHERE no_unit = 5394;
UPDATE inventory_unit SET
  customer_id = 30,
  customer_location_id = 98,
  kontrak_id = @contract_198,
  area_id = NULL,
  harga_sewa_bulanan = 9200000.0,
  on_hire_date = '2026-01-05',
  rate_changed_at = NOW()
WHERE no_unit = 5401;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = @contract_101,
  area_id = NULL,
  harga_sewa_bulanan = 62000000.0,
  on_hire_date = '2025-05-21',
  rate_changed_at = NOW()
WHERE no_unit = 5408;
UPDATE inventory_unit SET
  customer_id = 114,
  customer_location_id = @location_66,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-11-20',
  rate_changed_at = NOW()
WHERE no_unit = 3363;
UPDATE inventory_unit SET
  customer_id = 43,
  customer_location_id = 121,
  kontrak_id = @contract_124,
  area_id = NULL,
  harga_sewa_bulanan = 9900000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3364;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_50,
  area_id = 35,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2025-12-13',
  rate_changed_at = NOW()
WHERE no_unit = 5416;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 251,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2024-12-10',
  rate_changed_at = NOW()
WHERE no_unit = 3369;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 141,
  kontrak_id = @contract_84,
  area_id = NULL,
  harga_sewa_bulanan = 13800000.0,
  on_hire_date = '2023-05-31',
  rate_changed_at = NOW()
WHERE no_unit = 3373;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = @contract_12,
  area_id = NULL,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2023-07-04',
  rate_changed_at = NOW()
WHERE no_unit = 3374;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 240,
  kontrak_id = @contract_199,
  area_id = NULL,
  harga_sewa_bulanan = 17000000.0,
  on_hire_date = '2023-02-27',
  rate_changed_at = NOW()
WHERE no_unit = 3375;
UPDATE inventory_unit SET
  customer_id = 26,
  customer_location_id = 85,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2025-07-29',
  rate_changed_at = NOW()
WHERE no_unit = 3376;
UPDATE inventory_unit SET
  customer_id = 29,
  customer_location_id = 92,
  kontrak_id = @contract_200,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3377;
UPDATE inventory_unit SET
  customer_id = 29,
  customer_location_id = 92,
  kontrak_id = @contract_200,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3378;
UPDATE inventory_unit SET
  customer_id = 29,
  customer_location_id = 92,
  kontrak_id = @contract_200,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3379;
UPDATE inventory_unit SET
  customer_id = 29,
  customer_location_id = 92,
  kontrak_id = @contract_200,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3380;
UPDATE inventory_unit SET
  customer_id = 29,
  customer_location_id = 92,
  kontrak_id = @contract_200,
  area_id = NULL,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3381;
UPDATE inventory_unit SET
  customer_id = 29,
  customer_location_id = 92,
  kontrak_id = @contract_200,
  area_id = NULL,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3382;
UPDATE inventory_unit SET
  customer_id = 50,
  customer_location_id = @location_40,
  kontrak_id = @contract_118,
  area_id = NULL,
  harga_sewa_bulanan = 24500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3383;
UPDATE inventory_unit SET
  customer_id = 50,
  customer_location_id = @location_40,
  kontrak_id = @contract_118,
  area_id = NULL,
  harga_sewa_bulanan = 24500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3384;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = @contract_201,
  area_id = NULL,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3385;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 3386;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 3387;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 3388;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 3389;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = @location_44,
  kontrak_id = @contract_129,
  area_id = NULL,
  harga_sewa_bulanan = 12300000.0,
  on_hire_date = '2023-07-20',
  rate_changed_at = NOW()
WHERE no_unit = 3390;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = @contract_202,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3391;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = @contract_202,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3392;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = @location_44,
  kontrak_id = @contract_129,
  area_id = NULL,
  harga_sewa_bulanan = 12300000.0,
  on_hire_date = '2023-03-21',
  rate_changed_at = NOW()
WHERE no_unit = 3393;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 419,
  kontrak_id = @contract_35,
  area_id = NULL,
  harga_sewa_bulanan = 23500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1346;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_203,
  area_id = 26,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3394;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_203,
  area_id = 26,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3395;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_203,
  area_id = 26,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3396;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = 26,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3397;
UPDATE inventory_unit SET
  customer_id = 96,
  customer_location_id = 281,
  kontrak_id = @contract_204,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2025-11-13',
  rate_changed_at = NOW()
WHERE no_unit = 3398;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_205,
  area_id = 34,
  harga_sewa_bulanan = 41500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3400;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_206,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 3401;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_11,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2025-09-05',
  rate_changed_at = NOW()
WHERE no_unit = 5441;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_11,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2025-09-05',
  rate_changed_at = NOW()
WHERE no_unit = 5443;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = @location_44,
  kontrak_id = @contract_129,
  area_id = NULL,
  harga_sewa_bulanan = 12300000.0,
  on_hire_date = '2023-03-21',
  rate_changed_at = NOW()
WHERE no_unit = 3404;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = @location_44,
  kontrak_id = @contract_129,
  area_id = NULL,
  harga_sewa_bulanan = 12300000.0,
  on_hire_date = '2023-03-21',
  rate_changed_at = NOW()
WHERE no_unit = 3405;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = 270,
  kontrak_id = @contract_207,
  area_id = NULL,
  harga_sewa_bulanan = 22500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5445;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 14900000.0,
  on_hire_date = '2024-11-22',
  rate_changed_at = NOW()
WHERE no_unit = 5449;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = @contract_201,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5562;
UPDATE inventory_unit SET
  customer_id = 161,
  customer_location_id = @location_67,
  kontrak_id = @contract_208,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3409;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_205,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 3410;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 264,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 10800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3411;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 266,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11300000.0,
  on_hire_date = '2023-06-16',
  rate_changed_at = NOW()
WHERE no_unit = 3412;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = @location_68,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1365;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = @contract_109,
  area_id = NULL,
  harga_sewa_bulanan = 11300000.0,
  on_hire_date = '2023-04-05',
  rate_changed_at = NOW()
WHERE no_unit = 3413;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = @contract_109,
  area_id = NULL,
  harga_sewa_bulanan = 11300000.0,
  on_hire_date = '2023-04-05',
  rate_changed_at = NOW()
WHERE no_unit = 3414;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_31,
  kontrak_id = @contract_93,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = '2023-04-06',
  rate_changed_at = NOW()
WHERE no_unit = 3415;

-- Processed 600/2178 units

UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_31,
  kontrak_id = @contract_209,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = '2023-04-06',
  rate_changed_at = NOW()
WHERE no_unit = 3416;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_31,
  kontrak_id = @contract_93,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = '2023-04-06',
  rate_changed_at = NOW()
WHERE no_unit = 3417;
UPDATE inventory_unit SET
  customer_id = 50,
  customer_location_id = @location_40,
  kontrak_id = @contract_118,
  area_id = NULL,
  harga_sewa_bulanan = 10700000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3418;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_91,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3419;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3420;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_91,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3422;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_91,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3423;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-06-14',
  rate_changed_at = NOW()
WHERE no_unit = 5158;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3425;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 266,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 10150000.0,
  on_hire_date = '2025-01-24',
  rate_changed_at = NOW()
WHERE no_unit = 5467;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 266,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 9400000.0,
  on_hire_date = '2025-01-24',
  rate_changed_at = NOW()
WHERE no_unit = 5468;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_210,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3428;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5469;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5470;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5471;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5472;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5473;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5474;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5475;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5476;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5477;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 13,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 22030000.0,
  on_hire_date = '2024-05-30',
  rate_changed_at = NOW()
WHERE no_unit = 1390;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_91,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3438;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_91,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3440;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 249,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 11950000.0,
  on_hire_date = '2024-06-18',
  rate_changed_at = NOW()
WHERE no_unit = 5161;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_97,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5481;
UPDATE inventory_unit SET
  customer_id = 122,
  customer_location_id = 329,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = '2024-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 3443;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 264,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1396;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_91,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3444;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_91,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3445;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_211,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3446;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_210,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3447;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3449;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = 224,
  kontrak_id = @contract_139,
  area_id = NULL,
  harga_sewa_bulanan = 10100000.0,
  on_hire_date = '2025-02-26',
  rate_changed_at = NOW()
WHERE no_unit = 5162;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 8900000.0,
  on_hire_date = '2025-02-25',
  rate_changed_at = NOW()
WHERE no_unit = 5570;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3452;
UPDATE inventory_unit SET
  customer_id = 96,
  customer_location_id = 281,
  kontrak_id = @contract_172,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2024-12-26',
  rate_changed_at = NOW()
WHERE no_unit = 5501;
UPDATE inventory_unit SET
  customer_id = 96,
  customer_location_id = 281,
  kontrak_id = @contract_172,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2024-12-26',
  rate_changed_at = NOW()
WHERE no_unit = 5502;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3455;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 8900000.0,
  on_hire_date = '2025-02-25',
  rate_changed_at = NOW()
WHERE no_unit = 5571;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_81,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2024-04-18',
  rate_changed_at = NOW()
WHERE no_unit = 3457;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_23,
  area_id = 26,
  harga_sewa_bulanan = 22500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5509;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_212,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3463;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3471;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3472;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_91,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3473;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_213,
  area_id = 26,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3474;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_214,
  area_id = 26,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3475;
UPDATE inventory_unit SET
  customer_id = 21,
  customer_location_id = @location_26,
  kontrak_id = @contract_175,
  area_id = 18,
  harga_sewa_bulanan = 12900000.0,
  on_hire_date = '2023-09-26',
  rate_changed_at = NOW()
WHERE no_unit = 1428;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_213,
  area_id = 26,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3477;
UPDATE inventory_unit SET
  customer_id = 168,
  customer_location_id = 410,
  kontrak_id = @contract_127,
  area_id = NULL,
  harga_sewa_bulanan = 10650000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1430;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 421,
  kontrak_id = @contract_35,
  area_id = NULL,
  harga_sewa_bulanan = 15500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1432;
UPDATE inventory_unit SET
  customer_id = 148,
  customer_location_id = 386,
  kontrak_id = @contract_215,
  area_id = NULL,
  harga_sewa_bulanan = 14250000.0,
  on_hire_date = '2024-01-31',
  rate_changed_at = NOW()
WHERE no_unit = 5528;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = @contract_197,
  area_id = NULL,
  harga_sewa_bulanan = 32500000.0,
  on_hire_date = '2025-01-14',
  rate_changed_at = NOW()
WHERE no_unit = 5529;
UPDATE inventory_unit SET
  customer_id = 29,
  customer_location_id = 92,
  kontrak_id = @contract_200,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3483;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = @contract_197,
  area_id = NULL,
  harga_sewa_bulanan = 32500000.0,
  on_hire_date = '2025-01-14',
  rate_changed_at = NOW()
WHERE no_unit = 5530;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 52,
  kontrak_id = @contract_19,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3485;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_69,
  kontrak_id = @contract_216,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3487;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 379,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = '2024-03-01',
  rate_changed_at = NOW()
WHERE no_unit = 1442;
UPDATE inventory_unit SET
  customer_id = 30,
  customer_location_id = 95,
  kontrak_id = @contract_217,
  area_id = NULL,
  harga_sewa_bulanan = 16200000.0,
  on_hire_date = '2025-07-29',
  rate_changed_at = NOW()
WHERE no_unit = 3491;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = 224,
  kontrak_id = @contract_139,
  area_id = NULL,
  harga_sewa_bulanan = 10100000.0,
  on_hire_date = '2025-02-26',
  rate_changed_at = NOW()
WHERE no_unit = 5539;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = 26,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3494;
UPDATE inventory_unit SET
  customer_id = 227,
  customer_location_id = @location_70,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2022-01-24',
  rate_changed_at = NOW()
WHERE no_unit = 1453;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_206,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-26',
  rate_changed_at = NOW()
WHERE no_unit = 3502;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_205,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-25',
  rate_changed_at = NOW()
WHERE no_unit = 3503;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_218,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-25',
  rate_changed_at = NOW()
WHERE no_unit = 3504;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_206,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-26',
  rate_changed_at = NOW()
WHERE no_unit = 3505;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_219,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-25',
  rate_changed_at = NOW()
WHERE no_unit = 3506;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_220,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-25',
  rate_changed_at = NOW()
WHERE no_unit = 3507;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_205,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-25',
  rate_changed_at = NOW()
WHERE no_unit = 3508;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_206,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-26',
  rate_changed_at = NOW()
WHERE no_unit = 3509;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_206,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-26',
  rate_changed_at = NOW()
WHERE no_unit = 3510;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = @location_42,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1463;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = @contract_84,
  area_id = NULL,
  harga_sewa_bulanan = 13800000.0,
  on_hire_date = '2023-06-24',
  rate_changed_at = NOW()
WHERE no_unit = 3511;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = @contract_84,
  area_id = NULL,
  harga_sewa_bulanan = 13800000.0,
  on_hire_date = '2023-06-24',
  rate_changed_at = NOW()
WHERE no_unit = 3512;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = @contract_84,
  area_id = NULL,
  harga_sewa_bulanan = 13800000.0,
  on_hire_date = '2023-06-24',
  rate_changed_at = NOW()
WHERE no_unit = 3513;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_192,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-26',
  rate_changed_at = NOW()
WHERE no_unit = 3514;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 351,
  kontrak_id = @contract_221,
  area_id = NULL,
  harga_sewa_bulanan = 9250000.0,
  on_hire_date = '2025-11-17',
  rate_changed_at = NOW()
WHERE no_unit = 3515;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = @contract_84,
  area_id = NULL,
  harga_sewa_bulanan = 13800000.0,
  on_hire_date = '2023-06-24',
  rate_changed_at = NOW()
WHERE no_unit = 3516;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 41,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 12050000.0,
  on_hire_date = '2023-12-29',
  rate_changed_at = NOW()
WHERE no_unit = 3517;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_206,
  area_id = 34,
  harga_sewa_bulanan = 41500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3518;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_205,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 3519;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_219,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 3520;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_206,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 3521;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_192,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 3522;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_220,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-05-25',
  rate_changed_at = NOW()
WHERE no_unit = 3523;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_219,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 3524;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_222,
  area_id = 34,
  harga_sewa_bulanan = 20564000.0,
  on_hire_date = '2023-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 3526;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_192,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 3527;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_205,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 3528;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_206,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-05-26',
  rate_changed_at = NOW()
WHERE no_unit = 3529;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_220,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-05-26',
  rate_changed_at = NOW()
WHERE no_unit = 3530;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_222,
  area_id = 34,
  harga_sewa_bulanan = 42400000.0,
  on_hire_date = '2023-05-26',
  rate_changed_at = NOW()
WHERE no_unit = 3531;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_220,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-25',
  rate_changed_at = NOW()
WHERE no_unit = 3532;
UPDATE inventory_unit SET
  customer_id = 140,
  customer_location_id = 360,
  kontrak_id = @contract_114,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2023-06-15',
  rate_changed_at = NOW()
WHERE no_unit = 3533;
UPDATE inventory_unit SET
  customer_id = 177,
  customer_location_id = 422,
  kontrak_id = @contract_9,
  area_id = 21,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3534;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = @location_42,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3535;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 346,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2025-02-25',
  rate_changed_at = NOW()
WHERE no_unit = 5579;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_14,
  kontrak_id = @contract_223,
  area_id = 19,
  harga_sewa_bulanan = 10450000.0,
  on_hire_date = '2023-09-14',
  rate_changed_at = NOW()
WHERE no_unit = 3537;
UPDATE inventory_unit SET
  customer_id = 140,
  customer_location_id = 360,
  kontrak_id = @contract_114,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2023-06-08',
  rate_changed_at = NOW()
WHERE no_unit = 3538;

-- Processed 700/2178 units

UPDATE inventory_unit SET
  customer_id = 140,
  customer_location_id = 360,
  kontrak_id = @contract_114,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2023-06-08',
  rate_changed_at = NOW()
WHERE no_unit = 3539;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 350,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2025-02-27',
  rate_changed_at = NOW()
WHERE no_unit = 5580;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_53,
  kontrak_id = @contract_169,
  area_id = 19,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3541;
UPDATE inventory_unit SET
  customer_id = 152,
  customer_location_id = 392,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 8800000.0,
  on_hire_date = '2025-03-03',
  rate_changed_at = NOW()
WHERE no_unit = 5581;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = @location_44,
  kontrak_id = @contract_129,
  area_id = NULL,
  harga_sewa_bulanan = 13800000.0,
  on_hire_date = '2023-07-20',
  rate_changed_at = NOW()
WHERE no_unit = 3543;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_220,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-25',
  rate_changed_at = NOW()
WHERE no_unit = 3544;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_192,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-25',
  rate_changed_at = NOW()
WHERE no_unit = 3545;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_220,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-25',
  rate_changed_at = NOW()
WHERE no_unit = 3546;
UPDATE inventory_unit SET
  customer_id = 43,
  customer_location_id = 121,
  kontrak_id = @contract_124,
  area_id = NULL,
  harga_sewa_bulanan = 10550000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3547;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 33,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 12050000.0,
  on_hire_date = '2023-12-28',
  rate_changed_at = NOW()
WHERE no_unit = 3548;
UPDATE inventory_unit SET
  customer_id = 99,
  customer_location_id = @location_71,
  kontrak_id = @contract_176,
  area_id = NULL,
  harga_sewa_bulanan = 9250000.0,
  on_hire_date = '2024-09-07',
  rate_changed_at = NOW()
WHERE no_unit = 1501;
UPDATE inventory_unit SET
  customer_id = 188,
  customer_location_id = 433,
  kontrak_id = @contract_224,
  area_id = 13,
  harga_sewa_bulanan = 25000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5589;
UPDATE inventory_unit SET
  customer_id = 30,
  customer_location_id = 95,
  kontrak_id = @contract_225,
  area_id = NULL,
  harga_sewa_bulanan = 10710000.0,
  on_hire_date = '2025-10-31',
  rate_changed_at = NOW()
WHERE no_unit = 3551;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = @contract_226,
  area_id = 27,
  harga_sewa_bulanan = 24000000.0,
  on_hire_date = '2023-07-20',
  rate_changed_at = NOW()
WHERE no_unit = 3552;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = 223,
  kontrak_id = @contract_227,
  area_id = NULL,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = '2025-05-09',
  rate_changed_at = NOW()
WHERE no_unit = 5599;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 23,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 11850000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 3554;
UPDATE inventory_unit SET
  customer_id = 142,
  customer_location_id = @location_72,
  kontrak_id = @contract_228,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = '2025-08-01',
  rate_changed_at = NOW()
WHERE no_unit = 5602;
UPDATE inventory_unit SET
  customer_id = 79,
  customer_location_id = 228,
  kontrak_id = @contract_73,
  area_id = NULL,
  harga_sewa_bulanan = 30500000.0,
  on_hire_date = '2025-05-07',
  rate_changed_at = NOW()
WHERE no_unit = 5604;
UPDATE inventory_unit SET
  customer_id = 183,
  customer_location_id = 428,
  kontrak_id = @contract_229,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3557;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 123,
  kontrak_id = @contract_3,
  area_id = 17,
  harga_sewa_bulanan = 7700.0,
  on_hire_date = '2025-07-31',
  rate_changed_at = NOW()
WHERE no_unit = 3558;
UPDATE inventory_unit SET
  customer_id = 79,
  customer_location_id = 228,
  kontrak_id = @contract_73,
  area_id = NULL,
  harga_sewa_bulanan = 30500000.0,
  on_hire_date = '2025-05-07',
  rate_changed_at = NOW()
WHERE no_unit = 5606;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 52,
  kontrak_id = @contract_19,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5607;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 376,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 3250000.0,
  on_hire_date = '2021-08-06',
  rate_changed_at = NOW()
WHERE no_unit = 1513;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 241,
  kontrak_id = @contract_230,
  area_id = NULL,
  harga_sewa_bulanan = 11800000.0,
  on_hire_date = '2024-03-14',
  rate_changed_at = NOW()
WHERE no_unit = 3561;
UPDATE inventory_unit SET
  customer_id = 96,
  customer_location_id = 281,
  kontrak_id = @contract_204,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2025-11-13',
  rate_changed_at = NOW()
WHERE no_unit = 1515;
UPDATE inventory_unit SET
  customer_id = 88,
  customer_location_id = 259,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-10-27',
  rate_changed_at = NOW()
WHERE no_unit = 3562;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 241,
  kontrak_id = @contract_230,
  area_id = NULL,
  harga_sewa_bulanan = 11800000.0,
  on_hire_date = '2024-03-14',
  rate_changed_at = NOW()
WHERE no_unit = 3563;
UPDATE inventory_unit SET
  customer_id = 138,
  customer_location_id = 357,
  kontrak_id = @contract_231,
  area_id = 19,
  harga_sewa_bulanan = 16000000.0,
  on_hire_date = '2024-03-20',
  rate_changed_at = NOW()
WHERE no_unit = 3564;
UPDATE inventory_unit SET
  customer_id = 7,
  customer_location_id = @location_25,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 27000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3565;
UPDATE inventory_unit SET
  customer_id = 140,
  customer_location_id = 360,
  kontrak_id = @contract_114,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = '2024-10-07',
  rate_changed_at = NOW()
WHERE no_unit = 3566;
UPDATE inventory_unit SET
  customer_id = 140,
  customer_location_id = 360,
  kontrak_id = @contract_114,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2022-06-01',
  rate_changed_at = NOW()
WHERE no_unit = 3568;
UPDATE inventory_unit SET
  customer_id = 225,
  customer_location_id = @location_73,
  kontrak_id = NULL,
  area_id = 21,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3570;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_53,
  kontrak_id = @contract_169,
  area_id = 19,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3571;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_53,
  kontrak_id = @contract_169,
  area_id = 19,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3572;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_53,
  kontrak_id = @contract_169,
  area_id = 19,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3573;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_53,
  kontrak_id = @contract_169,
  area_id = 19,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3574;
UPDATE inventory_unit SET
  customer_id = 64,
  customer_location_id = @location_6,
  kontrak_id = @contract_150,
  area_id = NULL,
  harga_sewa_bulanan = 27000000.0,
  on_hire_date = '2025-04-28',
  rate_changed_at = NOW()
WHERE no_unit = 5614;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_53,
  kontrak_id = @contract_169,
  area_id = 19,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3576;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = @contract_65,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = '2023-11-22',
  rate_changed_at = NOW()
WHERE no_unit = 3577;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = @contract_65,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = '2023-11-23',
  rate_changed_at = NOW()
WHERE no_unit = 3578;
UPDATE inventory_unit SET
  customer_id = 64,
  customer_location_id = @location_6,
  kontrak_id = @contract_150,
  area_id = NULL,
  harga_sewa_bulanan = 11200000.0,
  on_hire_date = '2025-04-26',
  rate_changed_at = NOW()
WHERE no_unit = 5619;
UPDATE inventory_unit SET
  customer_id = 53,
  customer_location_id = @location_74,
  kontrak_id = @contract_232,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-05-02',
  rate_changed_at = NOW()
WHERE no_unit = 5620;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 251,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2024-06-07',
  rate_changed_at = NOW()
WHERE no_unit = 3581;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 6900000.0,
  on_hire_date = '2025-02-24',
  rate_changed_at = NOW()
WHERE no_unit = 3582;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = @contract_233,
  area_id = 18,
  harga_sewa_bulanan = 5750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5622;
UPDATE inventory_unit SET
  customer_id = 88,
  customer_location_id = 259,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-08-29',
  rate_changed_at = NOW()
WHERE no_unit = 3584;
UPDATE inventory_unit SET
  customer_id = 88,
  customer_location_id = 258,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-08-29',
  rate_changed_at = NOW()
WHERE no_unit = 3585;
UPDATE inventory_unit SET
  customer_id = 88,
  customer_location_id = 259,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-08-29',
  rate_changed_at = NOW()
WHERE no_unit = 3586;
UPDATE inventory_unit SET
  customer_id = 88,
  customer_location_id = 259,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-08-29',
  rate_changed_at = NOW()
WHERE no_unit = 3587;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 36,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 12050000.0,
  on_hire_date = '2023-12-18',
  rate_changed_at = NOW()
WHERE no_unit = 3588;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 40,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 12050000.0,
  on_hire_date = '2023-12-29',
  rate_changed_at = NOW()
WHERE no_unit = 3589;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 23,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 11850000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 3590;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 21,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 11850000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 3591;
UPDATE inventory_unit SET
  customer_id = 127,
  customer_location_id = 338,
  kontrak_id = @contract_234,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2023-06-17',
  rate_changed_at = NOW()
WHERE no_unit = 3592;
UPDATE inventory_unit SET
  customer_id = 88,
  customer_location_id = 259,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-08-29',
  rate_changed_at = NOW()
WHERE no_unit = 3593;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_39,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 32500000.0,
  on_hire_date = '2025-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 5635;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = @contract_197,
  area_id = NULL,
  harga_sewa_bulanan = 32500000.0,
  on_hire_date = '2025-04-30',
  rate_changed_at = NOW()
WHERE no_unit = 5636;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = @contract_197,
  area_id = NULL,
  harga_sewa_bulanan = 32500000.0,
  on_hire_date = '2025-04-30',
  rate_changed_at = NOW()
WHERE no_unit = 5637;
UPDATE inventory_unit SET
  customer_id = 79,
  customer_location_id = 228,
  kontrak_id = @contract_73,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2025-06-12',
  rate_changed_at = NOW()
WHERE no_unit = 5643;
UPDATE inventory_unit SET
  customer_id = 37,
  customer_location_id = 110,
  kontrak_id = @contract_235,
  area_id = 19,
  harga_sewa_bulanan = 9100000.0,
  on_hire_date = '2021-06-28',
  rate_changed_at = NOW()
WHERE no_unit = 3598;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_14,
  kontrak_id = @contract_223,
  area_id = 19,
  harga_sewa_bulanan = 9450000.0,
  on_hire_date = '2023-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 3605;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_14,
  kontrak_id = @contract_223,
  area_id = 19,
  harga_sewa_bulanan = 10450000.0,
  on_hire_date = '2023-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 3606;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_14,
  kontrak_id = @contract_223,
  area_id = 19,
  harga_sewa_bulanan = 10450000.0,
  on_hire_date = '2023-09-30',
  rate_changed_at = NOW()
WHERE no_unit = 3607;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 6900000.0,
  on_hire_date = '2025-02-21',
  rate_changed_at = NOW()
WHERE no_unit = 3609;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 6900000.0,
  on_hire_date = '2025-02-21',
  rate_changed_at = NOW()
WHERE no_unit = 3610;
UPDATE inventory_unit SET
  customer_id = 27,
  customer_location_id = 89,
  kontrak_id = @contract_236,
  area_id = 19,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = '2025-06-25',
  rate_changed_at = NOW()
WHERE no_unit = 5657;
UPDATE inventory_unit SET
  customer_id = 177,
  customer_location_id = 422,
  kontrak_id = @contract_9,
  area_id = 21,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3612;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 151,
  kontrak_id = @contract_237,
  area_id = NULL,
  harga_sewa_bulanan = 330000.0,
  on_hire_date = '2024-05-06',
  rate_changed_at = NOW()
WHERE no_unit = 3613;
UPDATE inventory_unit SET
  customer_id = 7,
  customer_location_id = @location_25,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 50000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3615;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_39,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 32500000.0,
  on_hire_date = '2025-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 5663;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_14,
  kontrak_id = @contract_223,
  area_id = 19,
  harga_sewa_bulanan = 9450000.0,
  on_hire_date = '2023-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 3617;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_14,
  kontrak_id = @contract_223,
  area_id = 19,
  harga_sewa_bulanan = 9450000.0,
  on_hire_date = '2023-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 3618;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_14,
  kontrak_id = @contract_223,
  area_id = 19,
  harga_sewa_bulanan = 9450000.0,
  on_hire_date = '2023-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 3619;
UPDATE inventory_unit SET
  customer_id = 121,
  customer_location_id = 328,
  kontrak_id = @contract_100,
  area_id = NULL,
  harga_sewa_bulanan = 14500000.0,
  on_hire_date = '2021-12-30',
  rate_changed_at = NOW()
WHERE no_unit = 5664;
UPDATE inventory_unit SET
  customer_id = 111,
  customer_location_id = 314,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2025-07-14',
  rate_changed_at = NOW()
WHERE no_unit = 5665;
UPDATE inventory_unit SET
  customer_id = 1,
  customer_location_id = @location_9,
  kontrak_id = @contract_24,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2025-03-19',
  rate_changed_at = NOW()
WHERE no_unit = 3622;
UPDATE inventory_unit SET
  customer_id = 183,
  customer_location_id = 428,
  kontrak_id = @contract_238,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3623;
UPDATE inventory_unit SET
  customer_id = 111,
  customer_location_id = 314,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2025-07-14',
  rate_changed_at = NOW()
WHERE no_unit = 5666;
UPDATE inventory_unit SET
  customer_id = 49,
  customer_location_id = 165,
  kontrak_id = @contract_239,
  area_id = 12,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2025-12-01',
  rate_changed_at = NOW()
WHERE no_unit = 3625;
UPDATE inventory_unit SET
  customer_id = 111,
  customer_location_id = 314,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 13250000.0,
  on_hire_date = '2025-07-14',
  rate_changed_at = NOW()
WHERE no_unit = 5667;
UPDATE inventory_unit SET
  customer_id = 111,
  customer_location_id = 314,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 13250000.0,
  on_hire_date = '2025-07-14',
  rate_changed_at = NOW()
WHERE no_unit = 5668;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = @contract_240,
  area_id = NULL,
  harga_sewa_bulanan = 10450000.0,
  on_hire_date = '2025-01-07',
  rate_changed_at = NOW()
WHERE no_unit = 3628;
UPDATE inventory_unit SET
  customer_id = 34,
  customer_location_id = 107,
  kontrak_id = @contract_241,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5673;
UPDATE inventory_unit SET
  customer_id = 26,
  customer_location_id = 85,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2025-04-21',
  rate_changed_at = NOW()
WHERE no_unit = 3630;
UPDATE inventory_unit SET
  customer_id = 184,
  customer_location_id = 429,
  kontrak_id = @contract_70,
  area_id = NULL,
  harga_sewa_bulanan = 10500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5676;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = 26,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 5677;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 6900000.0,
  on_hire_date = '2025-02-24',
  rate_changed_at = NOW()
WHERE no_unit = 3633;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 251,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2024-09-27',
  rate_changed_at = NOW()
WHERE no_unit = 3634;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = 26,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 5678;
UPDATE inventory_unit SET
  customer_id = 140,
  customer_location_id = 360,
  kontrak_id = @contract_114,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2022-06-01',
  rate_changed_at = NOW()
WHERE no_unit = 3636;
UPDATE inventory_unit SET
  customer_id = 122,
  customer_location_id = 329,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2025-06-13',
  rate_changed_at = NOW()
WHERE no_unit = 5679;
UPDATE inventory_unit SET
  customer_id = 122,
  customer_location_id = 329,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2025-06-12',
  rate_changed_at = NOW()
WHERE no_unit = 5680;
UPDATE inventory_unit SET
  customer_id = 226,
  customer_location_id = @location_75,
  kontrak_id = @contract_242,
  area_id = NULL,
  harga_sewa_bulanan = 50200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5682;
UPDATE inventory_unit SET
  customer_id = 79,
  customer_location_id = 228,
  kontrak_id = @contract_73,
  area_id = NULL,
  harga_sewa_bulanan = 30500000.0,
  on_hire_date = '2025-05-07',
  rate_changed_at = NOW()
WHERE no_unit = 5608;
UPDATE inventory_unit SET
  customer_id = 48,
  customer_location_id = @location_32,
  kontrak_id = @contract_99,
  area_id = NULL,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5684;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_39,
  kontrak_id = @contract_117,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = '2023-08-25',
  rate_changed_at = NOW()
WHERE no_unit = 3645;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_39,
  kontrak_id = @contract_117,
  area_id = NULL,
  harga_sewa_bulanan = 9700000.0,
  on_hire_date = '2023-08-25',
  rate_changed_at = NOW()
WHERE no_unit = 3646;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = 225,
  kontrak_id = @contract_243,
  area_id = NULL,
  harga_sewa_bulanan = 12400000.0,
  on_hire_date = '2025-05-08',
  rate_changed_at = NOW()
WHERE no_unit = 5609;
UPDATE inventory_unit SET
  customer_id = 40,
  customer_location_id = @location_76,
  kontrak_id = @contract_244,
  area_id = NULL,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = '2024-07-31',
  rate_changed_at = NOW()
WHERE no_unit = 1600;
UPDATE inventory_unit SET
  customer_id = 171,
  customer_location_id = 413,
  kontrak_id = @contract_245,
  area_id = NULL,
  harga_sewa_bulanan = 13500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1601;

-- Processed 800/2178 units

UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = @contract_246,
  area_id = NULL,
  harga_sewa_bulanan = 6950000.0,
  on_hire_date = '2024-12-23',
  rate_changed_at = NOW()
WHERE no_unit = 3650;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = 225,
  kontrak_id = @contract_243,
  area_id = NULL,
  harga_sewa_bulanan = 12400000.0,
  on_hire_date = '2025-05-08',
  rate_changed_at = NOW()
WHERE no_unit = 5610;
UPDATE inventory_unit SET
  customer_id = 226,
  customer_location_id = @location_75,
  kontrak_id = @contract_242,
  area_id = NULL,
  harga_sewa_bulanan = 9700000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3652;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 376,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 3250000.0,
  on_hire_date = '2021-08-21',
  rate_changed_at = NOW()
WHERE no_unit = 1605;
UPDATE inventory_unit SET
  customer_id = 163,
  customer_location_id = 405,
  kontrak_id = @contract_247,
  area_id = NULL,
  harga_sewa_bulanan = 41500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3653;
UPDATE inventory_unit SET
  customer_id = 177,
  customer_location_id = 422,
  kontrak_id = @contract_9,
  area_id = 21,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3654;
UPDATE inventory_unit SET
  customer_id = 64,
  customer_location_id = @location_6,
  kontrak_id = @contract_16,
  area_id = NULL,
  harga_sewa_bulanan = 10800000.0,
  on_hire_date = '2024-08-27',
  rate_changed_at = NOW()
WHERE no_unit = 1608;
UPDATE inventory_unit SET
  customer_id = 12,
  customer_location_id = 65,
  kontrak_id = @contract_248,
  area_id = NULL,
  harga_sewa_bulanan = 31000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3657;
UPDATE inventory_unit SET
  customer_id = 12,
  customer_location_id = 65,
  kontrak_id = @contract_249,
  area_id = NULL,
  harga_sewa_bulanan = 31000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3658;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = 223,
  kontrak_id = @contract_243,
  area_id = NULL,
  harga_sewa_bulanan = 12400000.0,
  on_hire_date = '2025-05-09',
  rate_changed_at = NOW()
WHERE no_unit = 5611;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = @contract_130,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3660;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_53,
  kontrak_id = @contract_169,
  area_id = 19,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3661;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = 223,
  kontrak_id = @contract_243,
  area_id = NULL,
  harga_sewa_bulanan = 12400000.0,
  on_hire_date = '2025-05-09',
  rate_changed_at = NOW()
WHERE no_unit = 5612;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = @location_77,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-09-05',
  rate_changed_at = NOW()
WHERE no_unit = 3663;
UPDATE inventory_unit SET
  customer_id = 193,
  customer_location_id = 438,
  kontrak_id = @contract_250,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5703;
UPDATE inventory_unit SET
  customer_id = 13,
  customer_location_id = @location_78,
  kontrak_id = @contract_251,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3665;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 6900000.0,
  on_hire_date = '2025-02-24',
  rate_changed_at = NOW()
WHERE no_unit = 3666;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = @contract_252,
  area_id = NULL,
  harga_sewa_bulanan = 6950000.0,
  on_hire_date = '2024-12-23',
  rate_changed_at = NOW()
WHERE no_unit = 3667;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = 223,
  kontrak_id = @contract_243,
  area_id = NULL,
  harga_sewa_bulanan = 12400000.0,
  on_hire_date = '2025-05-09',
  rate_changed_at = NOW()
WHERE no_unit = 5613;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 7100000.0,
  on_hire_date = '2025-02-24',
  rate_changed_at = NOW()
WHERE no_unit = 3669;
UPDATE inventory_unit SET
  customer_id = 140,
  customer_location_id = 360,
  kontrak_id = @contract_253,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = '2025-12-10',
  rate_changed_at = NOW()
WHERE no_unit = 5713;
UPDATE inventory_unit SET
  customer_id = 188,
  customer_location_id = 433,
  kontrak_id = @contract_254,
  area_id = 13,
  harga_sewa_bulanan = 22000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5714;
UPDATE inventory_unit SET
  customer_id = 122,
  customer_location_id = 332,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2025-07-22',
  rate_changed_at = NOW()
WHERE no_unit = 5718;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 6900000.0,
  on_hire_date = '2025-02-24',
  rate_changed_at = NOW()
WHERE no_unit = 3673;
UPDATE inventory_unit SET
  customer_id = 122,
  customer_location_id = 329,
  kontrak_id = @contract_255,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = '2025-11-17',
  rate_changed_at = NOW()
WHERE no_unit = 5719;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_10,
  kontrak_id = @contract_256,
  area_id = 19,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3675;
UPDATE inventory_unit SET
  customer_id = 88,
  customer_location_id = 258,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-07-25',
  rate_changed_at = NOW()
WHERE no_unit = 3676;
UPDATE inventory_unit SET
  customer_id = 88,
  customer_location_id = 258,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-08-15',
  rate_changed_at = NOW()
WHERE no_unit = 3677;
UPDATE inventory_unit SET
  customer_id = 88,
  customer_location_id = 258,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-08-18',
  rate_changed_at = NOW()
WHERE no_unit = 3678;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = @contract_84,
  area_id = NULL,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = '2023-07-24',
  rate_changed_at = NOW()
WHERE no_unit = 3679;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = @contract_84,
  area_id = NULL,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = '2023-07-24',
  rate_changed_at = NOW()
WHERE no_unit = 3680;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 380,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 9300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1633;
UPDATE inventory_unit SET
  customer_id = 64,
  customer_location_id = @location_79,
  kontrak_id = @contract_257,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2024-05-16',
  rate_changed_at = NOW()
WHERE no_unit = 1634;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = @contract_84,
  area_id = NULL,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = '2023-07-25',
  rate_changed_at = NOW()
WHERE no_unit = 3681;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = @contract_84,
  area_id = NULL,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = '2023-07-25',
  rate_changed_at = NOW()
WHERE no_unit = 3682;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = @contract_84,
  area_id = NULL,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = '2023-07-26',
  rate_changed_at = NOW()
WHERE no_unit = 3683;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = @contract_84,
  area_id = NULL,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = '2023-07-26',
  rate_changed_at = NOW()
WHERE no_unit = 3684;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 6900000.0,
  on_hire_date = '2025-02-24',
  rate_changed_at = NOW()
WHERE no_unit = 3686;
UPDATE inventory_unit SET
  customer_id = 226,
  customer_location_id = @location_75,
  kontrak_id = @contract_242,
  area_id = NULL,
  harga_sewa_bulanan = 10500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3687;
UPDATE inventory_unit SET
  customer_id = 72,
  customer_location_id = 218,
  kontrak_id = @contract_125,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3688;
UPDATE inventory_unit SET
  customer_id = 64,
  customer_location_id = @location_6,
  kontrak_id = @contract_150,
  area_id = NULL,
  harga_sewa_bulanan = 11200000.0,
  on_hire_date = '2025-04-26',
  rate_changed_at = NOW()
WHERE no_unit = 5617;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = @location_64,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2024-09-27',
  rate_changed_at = NOW()
WHERE no_unit = 3691;
UPDATE inventory_unit SET
  customer_id = 98,
  customer_location_id = 285,
  kontrak_id = @contract_258,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2023-10-02',
  rate_changed_at = NOW()
WHERE no_unit = 3692;
UPDATE inventory_unit SET
  customer_id = 88,
  customer_location_id = @location_80,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-07-28',
  rate_changed_at = NOW()
WHERE no_unit = 5734;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 173,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 10900000.0,
  on_hire_date = '2025-11-01',
  rate_changed_at = NOW()
WHERE no_unit = 3694;
UPDATE inventory_unit SET
  customer_id = 24,
  customer_location_id = @location_2,
  kontrak_id = @contract_259,
  area_id = 33,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = '2025-08-02',
  rate_changed_at = NOW()
WHERE no_unit = 5738;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_62,
  kontrak_id = @contract_185,
  area_id = 33,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3696;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = 271,
  kontrak_id = @contract_260,
  area_id = 18,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3697;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = 271,
  kontrak_id = @contract_261,
  area_id = 18,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3698;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_10,
  kontrak_id = @contract_262,
  area_id = 19,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3699;
UPDATE inventory_unit SET
  customer_id = 86,
  customer_location_id = @location_3,
  kontrak_id = @contract_8,
  area_id = NULL,
  harga_sewa_bulanan = 5500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1652;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_10,
  kontrak_id = @contract_263,
  area_id = 19,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3700;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_10,
  kontrak_id = @contract_264,
  area_id = 19,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3701;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = 271,
  kontrak_id = @contract_265,
  area_id = 18,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3702;
UPDATE inventory_unit SET
  customer_id = 122,
  customer_location_id = 329,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2023-10-03',
  rate_changed_at = NOW()
WHERE no_unit = 3703;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-12-03',
  rate_changed_at = NOW()
WHERE no_unit = 3704;
UPDATE inventory_unit SET
  customer_id = 30,
  customer_location_id = 97,
  kontrak_id = @contract_266,
  area_id = NULL,
  harga_sewa_bulanan = 10710000.0,
  on_hire_date = '2025-10-11',
  rate_changed_at = NOW()
WHERE no_unit = 3706;
UPDATE inventory_unit SET
  customer_id = 170,
  customer_location_id = 412,
  kontrak_id = @contract_267,
  area_id = NULL,
  harga_sewa_bulanan = 22000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1659;
UPDATE inventory_unit SET
  customer_id = 170,
  customer_location_id = 412,
  kontrak_id = @contract_267,
  area_id = NULL,
  harga_sewa_bulanan = 22000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1660;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-06-18',
  rate_changed_at = NOW()
WHERE no_unit = 3707;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = 271,
  kontrak_id = @contract_268,
  area_id = 18,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3708;
UPDATE inventory_unit SET
  customer_id = 96,
  customer_location_id = 281,
  kontrak_id = @contract_204,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2025-11-14',
  rate_changed_at = NOW()
WHERE no_unit = 3709;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 376,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 3250000.0,
  on_hire_date = '2021-08-05',
  rate_changed_at = NOW()
WHERE no_unit = 1664;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = 271,
  kontrak_id = @contract_268,
  area_id = 18,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3711;
UPDATE inventory_unit SET
  customer_id = 96,
  customer_location_id = 281,
  kontrak_id = @contract_204,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2025-11-13',
  rate_changed_at = NOW()
WHERE no_unit = 3713;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2023-12-22',
  rate_changed_at = NOW()
WHERE no_unit = 3715;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = @contract_233,
  area_id = 18,
  harga_sewa_bulanan = 5750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5623;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 266,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 9400000.0,
  on_hire_date = '2024-02-19',
  rate_changed_at = NOW()
WHERE no_unit = 3717;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_11,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2025-08-28',
  rate_changed_at = NOW()
WHERE no_unit = 5758;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = @contract_269,
  area_id = NULL,
  harga_sewa_bulanan = 10500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5759;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = @contract_270,
  area_id = 18,
  harga_sewa_bulanan = 5750000.0,
  on_hire_date = '2022-03-17',
  rate_changed_at = NOW()
WHERE no_unit = 5624;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_11,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-08-29',
  rate_changed_at = NOW()
WHERE no_unit = 5760;
UPDATE inventory_unit SET
  customer_id = 86,
  customer_location_id = @location_3,
  kontrak_id = @contract_8,
  area_id = NULL,
  harga_sewa_bulanan = 5500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1674;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 112,
  kontrak_id = @contract_271,
  area_id = NULL,
  harga_sewa_bulanan = 10050000.0,
  on_hire_date = '2025-08-21',
  rate_changed_at = NOW()
WHERE no_unit = 5763;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 113,
  kontrak_id = @contract_272,
  area_id = NULL,
  harga_sewa_bulanan = 10050000.0,
  on_hire_date = '2025-08-26',
  rate_changed_at = NOW()
WHERE no_unit = 5764;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = @contract_270,
  area_id = 18,
  harga_sewa_bulanan = 5750000.0,
  on_hire_date = '2022-03-17',
  rate_changed_at = NOW()
WHERE no_unit = 5625;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 114,
  kontrak_id = @contract_273,
  area_id = NULL,
  harga_sewa_bulanan = 10050000.0,
  on_hire_date = '2025-08-21',
  rate_changed_at = NOW()
WHERE no_unit = 5765;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 113,
  kontrak_id = @contract_274,
  area_id = NULL,
  harga_sewa_bulanan = 10050000.0,
  on_hire_date = '2025-08-21',
  rate_changed_at = NOW()
WHERE no_unit = 5766;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 112,
  kontrak_id = @contract_275,
  area_id = NULL,
  harga_sewa_bulanan = 10050000.0,
  on_hire_date = '2025-08-26',
  rate_changed_at = NOW()
WHERE no_unit = 5767;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 52,
  kontrak_id = @contract_19,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3729;
UPDATE inventory_unit SET
  customer_id = 118,
  customer_location_id = 322,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2021-08-21',
  rate_changed_at = NOW()
WHERE no_unit = 3730;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = @contract_276,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2023-09-25',
  rate_changed_at = NOW()
WHERE no_unit = 3731;
UPDATE inventory_unit SET
  customer_id = 129,
  customer_location_id = 342,
  kontrak_id = @contract_1,
  area_id = 19,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2022-05-13',
  rate_changed_at = NOW()
WHERE no_unit = 3732;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 152,
  kontrak_id = @contract_12,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2025-11-17',
  rate_changed_at = NOW()
WHERE no_unit = 3733;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = @contract_277,
  area_id = NULL,
  harga_sewa_bulanan = 6950000.0,
  on_hire_date = '2024-12-23',
  rate_changed_at = NOW()
WHERE no_unit = 3734;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = @contract_233,
  area_id = 18,
  harga_sewa_bulanan = 5750000.0,
  on_hire_date = '2024-04-29',
  rate_changed_at = NOW()
WHERE no_unit = 5626;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = @contract_233,
  area_id = 18,
  harga_sewa_bulanan = 5750000.0,
  on_hire_date = '2024-03-19',
  rate_changed_at = NOW()
WHERE no_unit = 5627;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = 232,
  kontrak_id = @contract_278,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2024-05-29',
  rate_changed_at = NOW()
WHERE no_unit = 3737;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 114,
  kontrak_id = @contract_279,
  area_id = NULL,
  harga_sewa_bulanan = 10050000.0,
  on_hire_date = '2025-08-26',
  rate_changed_at = NOW()
WHERE no_unit = 5779;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 114,
  kontrak_id = @contract_280,
  area_id = NULL,
  harga_sewa_bulanan = 10050000.0,
  on_hire_date = '2025-08-26',
  rate_changed_at = NOW()
WHERE no_unit = 5780;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = @contract_281,
  area_id = 18,
  harga_sewa_bulanan = 5750000.0,
  on_hire_date = '2022-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 5628;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = 232,
  kontrak_id = @contract_278,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = '2025-11-05',
  rate_changed_at = NOW()
WHERE no_unit = 3741;
UPDATE inventory_unit SET
  customer_id = 148,
  customer_location_id = 388,
  kontrak_id = @contract_135,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = '2025-08-20',
  rate_changed_at = NOW()
WHERE no_unit = 5781;
UPDATE inventory_unit SET
  customer_id = 122,
  customer_location_id = 329,
  kontrak_id = @contract_282,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = '2025-11-26',
  rate_changed_at = NOW()
WHERE no_unit = 5782;
UPDATE inventory_unit SET
  customer_id = 108,
  customer_location_id = 311,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2025-08-27',
  rate_changed_at = NOW()
WHERE no_unit = 5786;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 247,
  kontrak_id = @contract_65,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = '2023-11-22',
  rate_changed_at = NOW()
WHERE no_unit = 3745;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = @contract_283,
  area_id = 18,
  harga_sewa_bulanan = 5750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5629;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-09-15',
  rate_changed_at = NOW()
WHERE no_unit = 5791;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-09-15',
  rate_changed_at = NOW()
WHERE no_unit = 5792;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_11,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2025-08-29',
  rate_changed_at = NOW()
WHERE no_unit = 5794;

-- Processed 900/2178 units

UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = @contract_284,
  area_id = 18,
  harga_sewa_bulanan = 5750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5630;
UPDATE inventory_unit SET
  customer_id = 82,
  customer_location_id = 234,
  kontrak_id = NULL,
  area_id = 33,
  harga_sewa_bulanan = 5500000.0,
  on_hire_date = '2025-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 5797;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_10,
  kontrak_id = @contract_285,
  area_id = 19,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5798;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 5000000.0,
  on_hire_date = '2025-09-15',
  rate_changed_at = NOW()
WHERE no_unit = 5800;
UPDATE inventory_unit SET
  customer_id = 118,
  customer_location_id = 322,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-12-05',
  rate_changed_at = NOW()
WHERE no_unit = 3754;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = @contract_286,
  area_id = 18,
  harga_sewa_bulanan = 5750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5631;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 5000000.0,
  on_hire_date = '2025-09-15',
  rate_changed_at = NOW()
WHERE no_unit = 5801;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 5000000.0,
  on_hire_date = '2025-09-15',
  rate_changed_at = NOW()
WHERE no_unit = 5803;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = @location_81,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = '2025-03-20',
  rate_changed_at = NOW()
WHERE no_unit = 3758;
UPDATE inventory_unit SET
  customer_id = 108,
  customer_location_id = 311,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = '2025-09-02',
  rate_changed_at = NOW()
WHERE no_unit = 5804;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-09-15',
  rate_changed_at = NOW()
WHERE no_unit = 5805;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_14,
  kontrak_id = @contract_287,
  area_id = 19,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2023-10-19',
  rate_changed_at = NOW()
WHERE no_unit = 3761;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = @contract_288,
  area_id = NULL,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5806;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = @location_82,
  kontrak_id = @contract_289,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-12-02',
  rate_changed_at = NOW()
WHERE no_unit = 3763;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = @location_83,
  kontrak_id = @contract_290,
  area_id = 33,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-12-02',
  rate_changed_at = NOW()
WHERE no_unit = 3764;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = @location_84,
  kontrak_id = @contract_291,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-12-02',
  rate_changed_at = NOW()
WHERE no_unit = 3765;
UPDATE inventory_unit SET
  customer_id = 74,
  customer_location_id = @location_85,
  kontrak_id = @contract_292,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-11-16',
  rate_changed_at = NOW()
WHERE no_unit = 3766;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = @location_86,
  kontrak_id = @contract_293,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-12-05',
  rate_changed_at = NOW()
WHERE no_unit = 3767;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = @location_87,
  kontrak_id = @contract_294,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-12-06',
  rate_changed_at = NOW()
WHERE no_unit = 3768;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = @location_88,
  kontrak_id = @contract_295,
  area_id = 13,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-12-08',
  rate_changed_at = NOW()
WHERE no_unit = 3769;
UPDATE inventory_unit SET
  customer_id = 30,
  customer_location_id = 96,
  kontrak_id = @contract_296,
  area_id = NULL,
  harga_sewa_bulanan = 16524000.0,
  on_hire_date = '2025-09-03',
  rate_changed_at = NOW()
WHERE no_unit = 5809;
UPDATE inventory_unit SET
  customer_id = 74,
  customer_location_id = @location_89,
  kontrak_id = @contract_297,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-11-16',
  rate_changed_at = NOW()
WHERE no_unit = 3771;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = @location_90,
  kontrak_id = @contract_298,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-12-06',
  rate_changed_at = NOW()
WHERE no_unit = 3772;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = @contract_56,
  area_id = NULL,
  harga_sewa_bulanan = 19500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5812;
UPDATE inventory_unit SET
  customer_id = 7,
  customer_location_id = @location_25,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5813;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_11,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-09-26',
  rate_changed_at = NOW()
WHERE no_unit = 5814;
UPDATE inventory_unit SET
  customer_id = 146,
  customer_location_id = 384,
  kontrak_id = @contract_299,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = '2025-10-10',
  rate_changed_at = NOW()
WHERE no_unit = 5820;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 7100000.0,
  on_hire_date = '2025-02-24',
  rate_changed_at = NOW()
WHERE no_unit = 3777;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 5000000.0,
  on_hire_date = '2025-09-15',
  rate_changed_at = NOW()
WHERE no_unit = 5822;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-09-15',
  rate_changed_at = NOW()
WHERE no_unit = 5823;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = 26,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3783;
UPDATE inventory_unit SET
  customer_id = 164,
  customer_location_id = 406,
  kontrak_id = @contract_300,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1736;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = 26,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3784;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = 26,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3785;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = 26,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3786;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 3787;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = @contract_301,
  area_id = NULL,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3788;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 3790;
UPDATE inventory_unit SET
  customer_id = 152,
  customer_location_id = 392,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 8800000.0,
  on_hire_date = '2025-03-24',
  rate_changed_at = NOW()
WHERE no_unit = 5582;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = 26,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3792;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = @location_91,
  kontrak_id = @contract_302,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-12-06',
  rate_changed_at = NOW()
WHERE no_unit = 3793;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = @location_92,
  kontrak_id = @contract_303,
  area_id = 2,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-12-12',
  rate_changed_at = NOW()
WHERE no_unit = 3794;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = @location_93,
  kontrak_id = @contract_304,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-12-12',
  rate_changed_at = NOW()
WHERE no_unit = 3795;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = @location_94,
  kontrak_id = @contract_305,
  area_id = 16,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-12-13',
  rate_changed_at = NOW()
WHERE no_unit = 3796;
UPDATE inventory_unit SET
  customer_id = 42,
  customer_location_id = @location_95,
  kontrak_id = @contract_306,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = '2025-11-06',
  rate_changed_at = NOW()
WHERE no_unit = 3798;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 21,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-12-22',
  rate_changed_at = NOW()
WHERE no_unit = 3801;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 27,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 10800000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 3802;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 34,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-12-28',
  rate_changed_at = NOW()
WHERE no_unit = 3803;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 32,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-12-28',
  rate_changed_at = NOW()
WHERE no_unit = 3804;
UPDATE inventory_unit SET
  customer_id = 102,
  customer_location_id = 290,
  kontrak_id = @contract_126,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1760;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2024-04-29',
  rate_changed_at = NOW()
WHERE no_unit = 3811;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2024-04-29',
  rate_changed_at = NOW()
WHERE no_unit = 3812;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2024-04-29',
  rate_changed_at = NOW()
WHERE no_unit = 3815;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_211,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3817;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_211,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3818;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_211,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3819;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_211,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3820;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 149,
  kontrak_id = @contract_276,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2023-09-25',
  rate_changed_at = NOW()
WHERE no_unit = 1773;
UPDATE inventory_unit SET
  customer_id = 146,
  customer_location_id = 384,
  kontrak_id = @contract_307,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = '2024-02-02',
  rate_changed_at = NOW()
WHERE no_unit = 3822;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_211,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3823;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = @contract_308,
  area_id = NULL,
  harga_sewa_bulanan = 6800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5865;
UPDATE inventory_unit SET
  customer_id = 62,
  customer_location_id = @location_96,
  kontrak_id = @contract_309,
  area_id = NULL,
  harga_sewa_bulanan = 37000000.0,
  on_hire_date = '2022-07-18',
  rate_changed_at = NOW()
WHERE no_unit = 1777;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_211,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3825;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = @contract_308,
  area_id = NULL,
  harga_sewa_bulanan = 6800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5866;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_211,
  area_id = 26,
  harga_sewa_bulanan = 3170000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3828;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 5200000.0,
  on_hire_date = '2025-02-20',
  rate_changed_at = NOW()
WHERE no_unit = 3830;
UPDATE inventory_unit SET
  customer_id = 225,
  customer_location_id = @location_73,
  kontrak_id = NULL,
  area_id = 21,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1783;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 246,
  kontrak_id = @contract_65,
  area_id = NULL,
  harga_sewa_bulanan = 8200000.0,
  on_hire_date = '2023-11-24',
  rate_changed_at = NOW()
WHERE no_unit = 3832;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 245,
  kontrak_id = @contract_65,
  area_id = NULL,
  harga_sewa_bulanan = 8200000.0,
  on_hire_date = '2023-11-24',
  rate_changed_at = NOW()
WHERE no_unit = 3833;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 247,
  kontrak_id = @contract_65,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2023-11-24',
  rate_changed_at = NOW()
WHERE no_unit = 3834;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 249,
  kontrak_id = @contract_65,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-11-23',
  rate_changed_at = NOW()
WHERE no_unit = 3835;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 249,
  kontrak_id = @contract_65,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2023-11-24',
  rate_changed_at = NOW()
WHERE no_unit = 3836;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = @contract_308,
  area_id = NULL,
  harga_sewa_bulanan = 6800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5882;
UPDATE inventory_unit SET
  customer_id = 90,
  customer_location_id = @location_34,
  kontrak_id = @contract_103,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3838;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 248,
  kontrak_id = @contract_65,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = '2023-11-22',
  rate_changed_at = NOW()
WHERE no_unit = 3841;
UPDATE inventory_unit SET
  customer_id = 84,
  customer_location_id = @location_97,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2024-01-11',
  rate_changed_at = NOW()
WHERE no_unit = 3843;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_5,
  kontrak_id = @contract_13,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5893;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 251,
  kontrak_id = @contract_65,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = '2023-11-23',
  rate_changed_at = NOW()
WHERE no_unit = 3846;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_11,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-10-03',
  rate_changed_at = NOW()
WHERE no_unit = 5894;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = @contract_310,
  area_id = NULL,
  harga_sewa_bulanan = 13250000.0,
  on_hire_date = '2025-01-07',
  rate_changed_at = NOW()
WHERE no_unit = 3848;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 248,
  kontrak_id = @contract_65,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-11-24',
  rate_changed_at = NOW()
WHERE no_unit = 3849;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2023-11-22',
  rate_changed_at = NOW()
WHERE no_unit = 3850;
UPDATE inventory_unit SET
  customer_id = 84,
  customer_location_id = @location_97,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3851;
UPDATE inventory_unit SET
  customer_id = 84,
  customer_location_id = @location_98,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3852;
UPDATE inventory_unit SET
  customer_id = 69,
  customer_location_id = 213,
  kontrak_id = @contract_311,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = '2024-07-31',
  rate_changed_at = NOW()
WHERE no_unit = 3853;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = @location_19,
  kontrak_id = @contract_312,
  area_id = 20,
  harga_sewa_bulanan = 28500000.0,
  on_hire_date = '2024-10-07',
  rate_changed_at = NOW()
WHERE no_unit = 3854;
UPDATE inventory_unit SET
  customer_id = 69,
  customer_location_id = 213,
  kontrak_id = @contract_313,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = '2024-07-31',
  rate_changed_at = NOW()
WHERE no_unit = 3855;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = @contract_240,
  area_id = NULL,
  harga_sewa_bulanan = 6950000.0,
  on_hire_date = '2024-12-23',
  rate_changed_at = NOW()
WHERE no_unit = 3856;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = @contract_308,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5907;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = @location_99,
  kontrak_id = @contract_314,
  area_id = NULL,
  harga_sewa_bulanan = 15300000.0,
  on_hire_date = '2024-06-25',
  rate_changed_at = NOW()
WHERE no_unit = 3860;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 5200000.0,
  on_hire_date = '2025-02-20',
  rate_changed_at = NOW()
WHERE no_unit = 3861;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 5200000.0,
  on_hire_date = '2025-02-20',
  rate_changed_at = NOW()
WHERE no_unit = 3862;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 5200000.0,
  on_hire_date = '2025-02-20',
  rate_changed_at = NOW()
WHERE no_unit = 3863;
UPDATE inventory_unit SET
  customer_id = 177,
  customer_location_id = 422,
  kontrak_id = @contract_9,
  area_id = 21,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1816;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 5200000.0,
  on_hire_date = '2025-02-20',
  rate_changed_at = NOW()
WHERE no_unit = 3864;
UPDATE inventory_unit SET
  customer_id = 44,
  customer_location_id = 122,
  kontrak_id = @contract_315,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = '2025-08-20',
  rate_changed_at = NOW()
WHERE no_unit = 3867;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = @contract_56,
  area_id = NULL,
  harga_sewa_bulanan = 10500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5916;
UPDATE inventory_unit SET
  customer_id = 174,
  customer_location_id = 416,
  kontrak_id = @contract_116,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3869;
UPDATE inventory_unit SET
  customer_id = 110,
  customer_location_id = 313,
  kontrak_id = @contract_316,
  area_id = NULL,
  harga_sewa_bulanan = 14800000.0,
  on_hire_date = '2024-06-04',
  rate_changed_at = NOW()
WHERE no_unit = 3870;
UPDATE inventory_unit SET
  customer_id = 49,
  customer_location_id = 162,
  kontrak_id = @contract_317,
  area_id = NULL,
  harga_sewa_bulanan = 9739681.0,
  on_hire_date = '2024-11-15',
  rate_changed_at = NOW()
WHERE no_unit = 3874;

-- Processed 1000/2178 units

UPDATE inventory_unit SET
  customer_id = 229,
  customer_location_id = @location_22,
  kontrak_id = @contract_35,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1828;
UPDATE inventory_unit SET
  customer_id = 64,
  customer_location_id = 191,
  kontrak_id = @contract_257,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-05-16',
  rate_changed_at = NOW()
WHERE no_unit = 3878;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2024-05-01',
  rate_changed_at = NOW()
WHERE no_unit = 3879;
UPDATE inventory_unit SET
  customer_id = 142,
  customer_location_id = 374,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = '2024-07-31',
  rate_changed_at = NOW()
WHERE no_unit = 3884;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_100,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5933;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_39,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 10900000.0,
  on_hire_date = '2025-11-04',
  rate_changed_at = NOW()
WHERE no_unit = 3888;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 419,
  kontrak_id = @contract_35,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3892;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 421,
  kontrak_id = @contract_35,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3893;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 237,
  kontrak_id = @contract_318,
  area_id = NULL,
  harga_sewa_bulanan = 17800000.0,
  on_hire_date = '2023-11-23',
  rate_changed_at = NOW()
WHERE no_unit = 3902;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 240,
  kontrak_id = @contract_199,
  area_id = NULL,
  harga_sewa_bulanan = 17000000.0,
  on_hire_date = '2023-02-27',
  rate_changed_at = NOW()
WHERE no_unit = 3906;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 21,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 3912;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 21,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-12-22',
  rate_changed_at = NOW()
WHERE no_unit = 3913;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 22,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 3914;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 25,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 3915;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 26,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 3916;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 28,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 10800000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 3917;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 42,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 10800000.0,
  on_hire_date = '2023-12-21',
  rate_changed_at = NOW()
WHERE no_unit = 3918;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 29,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 3919;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 30,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-12-28',
  rate_changed_at = NOW()
WHERE no_unit = 3920;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 31,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-12-28',
  rate_changed_at = NOW()
WHERE no_unit = 3921;
UPDATE inventory_unit SET
  customer_id = 100,
  customer_location_id = @location_48,
  kontrak_id = @contract_319,
  area_id = NULL,
  harga_sewa_bulanan = 7250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3922;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = @location_49,
  kontrak_id = @contract_320,
  area_id = NULL,
  harga_sewa_bulanan = 39900000.0,
  on_hire_date = '2024-10-29',
  rate_changed_at = NOW()
WHERE no_unit = 3924;
UPDATE inventory_unit SET
  customer_id = 211,
  customer_location_id = @location_101,
  kontrak_id = @contract_321,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2024-09-28',
  rate_changed_at = NOW()
WHERE no_unit = 3927;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = 232,
  kontrak_id = @contract_322,
  area_id = NULL,
  harga_sewa_bulanan = 38900000.0,
  on_hire_date = '2024-10-28',
  rate_changed_at = NOW()
WHERE no_unit = 3930;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = 232,
  kontrak_id = @contract_323,
  area_id = NULL,
  harga_sewa_bulanan = 38900000.0,
  on_hire_date = '2024-10-28',
  rate_changed_at = NOW()
WHERE no_unit = 3931;
UPDATE inventory_unit SET
  customer_id = 199,
  customer_location_id = @location_102,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = '2023-10-02',
  rate_changed_at = NOW()
WHERE no_unit = 1885;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = 232,
  kontrak_id = @contract_322,
  area_id = NULL,
  harga_sewa_bulanan = 38900000.0,
  on_hire_date = '2024-10-28',
  rate_changed_at = NOW()
WHERE no_unit = 3934;
UPDATE inventory_unit SET
  customer_id = 62,
  customer_location_id = @location_96,
  kontrak_id = @contract_324,
  area_id = 12,
  harga_sewa_bulanan = 26500000.0,
  on_hire_date = '2025-10-20',
  rate_changed_at = NOW()
WHERE no_unit = 5985;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = @location_103,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3938;
UPDATE inventory_unit SET
  customer_id = 100,
  customer_location_id = @location_48,
  kontrak_id = @contract_325,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3939;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = @contract_308,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5990;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = @contract_308,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5992;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 351,
  kontrak_id = @contract_326,
  area_id = NULL,
  harga_sewa_bulanan = 6800000.0,
  on_hire_date = '2025-10-22',
  rate_changed_at = NOW()
WHERE no_unit = 5994;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 351,
  kontrak_id = @contract_326,
  area_id = NULL,
  harga_sewa_bulanan = 6800000.0,
  on_hire_date = '2025-10-22',
  rate_changed_at = NOW()
WHERE no_unit = 5995;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_104,
  kontrak_id = @contract_327,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6000;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_104,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6001;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_104,
  kontrak_id = @contract_327,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6002;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = @location_19,
  kontrak_id = @contract_328,
  area_id = 20,
  harga_sewa_bulanan = 48500000.0,
  on_hire_date = '2024-10-07',
  rate_changed_at = NOW()
WHERE no_unit = 3955;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_5,
  kontrak_id = @contract_329,
  area_id = 35,
  harga_sewa_bulanan = 22500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3956;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_104,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6003;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_104,
  kontrak_id = @contract_73,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6004;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_104,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6006;
UPDATE inventory_unit SET
  customer_id = 7,
  customer_location_id = @location_25,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 58000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3960;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_104,
  kontrak_id = @contract_327,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6007;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_104,
  kontrak_id = @contract_327,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6009;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = @location_105,
  kontrak_id = @contract_308,
  area_id = NULL,
  harga_sewa_bulanan = 6800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6011;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_104,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6013;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_104,
  kontrak_id = @contract_327,
  area_id = NULL,
  harga_sewa_bulanan = 7800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6014;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_104,
  kontrak_id = @contract_327,
  area_id = NULL,
  harga_sewa_bulanan = 16000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6015;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_104,
  kontrak_id = @contract_327,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6018;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = @location_64,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2024-06-10',
  rate_changed_at = NOW()
WHERE no_unit = 3971;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_104,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6020;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = @location_106,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2024-07-17',
  rate_changed_at = NOW()
WHERE no_unit = 3974;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_10,
  kontrak_id = @contract_330,
  area_id = 19,
  harga_sewa_bulanan = 7100000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6023;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_10,
  kontrak_id = @contract_331,
  area_id = 19,
  harga_sewa_bulanan = 7100000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6024;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_10,
  kontrak_id = @contract_332,
  area_id = 19,
  harga_sewa_bulanan = 7100000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6025;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 376,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 3250000.0,
  on_hire_date = '2021-08-16',
  rate_changed_at = NOW()
WHERE no_unit = 1930;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = @location_64,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2024-10-24',
  rate_changed_at = NOW()
WHERE no_unit = 1931;
UPDATE inventory_unit SET
  customer_id = 49,
  customer_location_id = 166,
  kontrak_id = @contract_333,
  area_id = NULL,
  harga_sewa_bulanan = 14500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6026;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = @contract_240,
  area_id = NULL,
  harga_sewa_bulanan = 6950000.0,
  on_hire_date = '2024-12-23',
  rate_changed_at = NOW()
WHERE no_unit = 3981;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 264,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11900000.0,
  on_hire_date = '2024-02-22',
  rate_changed_at = NOW()
WHERE no_unit = 3982;
UPDATE inventory_unit SET
  customer_id = 24,
  customer_location_id = @location_2,
  kontrak_id = @contract_334,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = '2025-12-13',
  rate_changed_at = NOW()
WHERE no_unit = 6029;
UPDATE inventory_unit SET
  customer_id = 82,
  customer_location_id = 234,
  kontrak_id = NULL,
  area_id = 33,
  harga_sewa_bulanan = 19500000.0,
  on_hire_date = '2025-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 6031;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 379,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = '2024-03-15',
  rate_changed_at = NOW()
WHERE no_unit = 3985;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 266,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 10800000.0,
  on_hire_date = '2022-01-31',
  rate_changed_at = NOW()
WHERE no_unit = 1938;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 24,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 11850000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 3986;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = @location_107,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 11850000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 3988;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 43,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 11850000.0,
  on_hire_date = '2023-12-21',
  rate_changed_at = NOW()
WHERE no_unit = 3989;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 35,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 11850000.0,
  on_hire_date = '2023-12-28',
  rate_changed_at = NOW()
WHERE no_unit = 3990;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 37,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 10800000.0,
  on_hire_date = '2023-12-29',
  rate_changed_at = NOW()
WHERE no_unit = 3991;
UPDATE inventory_unit SET
  customer_id = 130,
  customer_location_id = @location_108,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = '2022-12-07',
  rate_changed_at = NOW()
WHERE no_unit = 1944;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 38,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-12-29',
  rate_changed_at = NOW()
WHERE no_unit = 3992;
UPDATE inventory_unit SET
  customer_id = 122,
  customer_location_id = 333,
  kontrak_id = @contract_335,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2025-11-25',
  rate_changed_at = NOW()
WHERE no_unit = 1946;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 39,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-12-29',
  rate_changed_at = NOW()
WHERE no_unit = 3993;
UPDATE inventory_unit SET
  customer_id = 138,
  customer_location_id = 357,
  kontrak_id = @contract_231,
  area_id = 19,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = '2024-03-21',
  rate_changed_at = NOW()
WHERE no_unit = 3994;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = @location_107,
  kontrak_id = @contract_120,
  area_id = NULL,
  harga_sewa_bulanan = 11850000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 3995;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 265,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 10900000.0,
  on_hire_date = '2024-07-05',
  rate_changed_at = NOW()
WHERE no_unit = 3996;
UPDATE inventory_unit SET
  customer_id = 170,
  customer_location_id = @location_30,
  kontrak_id = @contract_90,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3997;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 379,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = '2024-07-23',
  rate_changed_at = NOW()
WHERE no_unit = 1952;
UPDATE inventory_unit SET
  customer_id = 170,
  customer_location_id = @location_30,
  kontrak_id = @contract_90,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3998;
UPDATE inventory_unit SET
  customer_id = 170,
  customer_location_id = @location_30,
  kontrak_id = @contract_90,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3999;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = 232,
  kontrak_id = @contract_336,
  area_id = NULL,
  harga_sewa_bulanan = 36500000.0,
  on_hire_date = '2025-11-29',
  rate_changed_at = NOW()
WHERE no_unit = 6044;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 377,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 9300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1956;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = 232,
  kontrak_id = @contract_337,
  area_id = NULL,
  harga_sewa_bulanan = 57500000.0,
  on_hire_date = '2025-12-01',
  rate_changed_at = NOW()
WHERE no_unit = 6045;
UPDATE inventory_unit SET
  customer_id = 79,
  customer_location_id = 228,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2021-06-25',
  rate_changed_at = NOW()
WHERE no_unit = 1974;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 239,
  kontrak_id = @contract_338,
  area_id = NULL,
  harga_sewa_bulanan = 26500000.0,
  on_hire_date = '2026-01-09',
  rate_changed_at = NOW()
WHERE no_unit = 6070;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 377,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 9300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1976;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_11,
  kontrak_id = @contract_143,
  area_id = 19,
  harga_sewa_bulanan = 16000000.0,
  on_hire_date = '2024-12-03',
  rate_changed_at = NOW()
WHERE no_unit = 1977;
UPDATE inventory_unit SET
  customer_id = 49,
  customer_location_id = 166,
  kontrak_id = @contract_333,
  area_id = NULL,
  harga_sewa_bulanan = 14500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1978;
UPDATE inventory_unit SET
  customer_id = 25,
  customer_location_id = 84,
  kontrak_id = @contract_339,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1979;
UPDATE inventory_unit SET
  customer_id = 103,
  customer_location_id = @location_109,
  kontrak_id = @contract_340,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2026-01-14',
  rate_changed_at = NOW()
WHERE no_unit = 6072;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = @contract_156,
  area_id = NULL,
  harga_sewa_bulanan = 24000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5584;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 247,
  kontrak_id = @contract_65,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = '2024-10-04',
  rate_changed_at = NOW()
WHERE no_unit = 1993;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = 223,
  kontrak_id = @contract_227,
  area_id = NULL,
  harga_sewa_bulanan = 17300000.0,
  on_hire_date = '2025-05-13',
  rate_changed_at = NOW()
WHERE no_unit = 5615;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = @location_21,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 6800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2001;
UPDATE inventory_unit SET
  customer_id = 68,
  customer_location_id = @location_110,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2013;
UPDATE inventory_unit SET
  customer_id = 204,
  customer_location_id = 454,
  kontrak_id = @contract_341,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2025-05-02',
  rate_changed_at = NOW()
WHERE no_unit = 2018;
UPDATE inventory_unit SET
  customer_id = 96,
  customer_location_id = @location_55,
  kontrak_id = @contract_173,
  area_id = NULL,
  harga_sewa_bulanan = 11750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5286;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = 223,
  kontrak_id = @contract_227,
  area_id = NULL,
  harga_sewa_bulanan = 17300000.0,
  on_hire_date = '2025-05-13',
  rate_changed_at = NOW()
WHERE no_unit = 5616;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_111,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2052;

-- Processed 1100/2178 units

UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = 15,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2057;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = @location_113,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2060;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = @location_64,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2065;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2070;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = @contract_343,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2086;
UPDATE inventory_unit SET
  customer_id = 41,
  customer_location_id = @location_115,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2093;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_116,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2112;
UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = @location_117,
  kontrak_id = @contract_344,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2113;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_118,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2114;
UPDATE inventory_unit SET
  customer_id = 41,
  customer_location_id = @location_115,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2115;
UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = @location_117,
  kontrak_id = @contract_345,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2117;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2130;
UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = @location_117,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2138;
UPDATE inventory_unit SET
  customer_id = 99,
  customer_location_id = @location_119,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2147;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = @location_64,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2168;
UPDATE inventory_unit SET
  customer_id = 177,
  customer_location_id = @location_120,
  kontrak_id = NULL,
  area_id = 21,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2169;
UPDATE inventory_unit SET
  customer_id = 29,
  customer_location_id = @location_121,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2198;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_122,
  kontrak_id = @contract_117,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2209;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_122,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2215;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = @contract_343,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2217;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_122,
  kontrak_id = @contract_117,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2227;
UPDATE inventory_unit SET
  customer_id = 166,
  customer_location_id = @location_123,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2229;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = @location_124,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2236;
UPDATE inventory_unit SET
  customer_id = 184,
  customer_location_id = @location_125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2237;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = @location_126,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2251;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = @location_127,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2258;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = @location_124,
  kontrak_id = @contract_346,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2261;
UPDATE inventory_unit SET
  customer_id = 99,
  customer_location_id = @location_119,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2268;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = @location_127,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2283;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 43,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2295;
UPDATE inventory_unit SET
  customer_id = 34,
  customer_location_id = @location_128,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2298;
UPDATE inventory_unit SET
  customer_id = 99,
  customer_location_id = @location_119,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2299;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = @location_124,
  kontrak_id = @contract_347,
  area_id = 34,
  harga_sewa_bulanan = 10500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2300;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = @location_127,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2301;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = @location_127,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2303;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = @location_127,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2304;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = @location_127,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2305;
UPDATE inventory_unit SET
  customer_id = 98,
  customer_location_id = @location_129,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2306;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = @location_124,
  kontrak_id = @contract_348,
  area_id = 33,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2307;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = @location_124,
  kontrak_id = @contract_349,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2308;
UPDATE inventory_unit SET
  customer_id = 220,
  customer_location_id = @location_130,
  kontrak_id = @contract_228,
  area_id = 33,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2320;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_122,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2324;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = @location_131,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2336;
UPDATE inventory_unit SET
  customer_id = 177,
  customer_location_id = @location_120,
  kontrak_id = NULL,
  area_id = 21,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2350;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_122,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2352;
UPDATE inventory_unit SET
  customer_id = 99,
  customer_location_id = @location_119,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2354;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = @contract_350,
  area_id = 27,
  harga_sewa_bulanan = 70000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2360;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = @contract_350,
  area_id = 27,
  harga_sewa_bulanan = 70000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2361;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = @contract_350,
  area_id = 27,
  harga_sewa_bulanan = 70000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2362;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = @location_133,
  kontrak_id = @contract_351,
  area_id = NULL,
  harga_sewa_bulanan = 13200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2369;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = @location_131,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2373;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = @location_131,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2374;
UPDATE inventory_unit SET
  customer_id = 199,
  customer_location_id = @location_134,
  kontrak_id = @contract_352,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2395;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_111,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2408;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = @location_64,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2410;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_116,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2411;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 25300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2416;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2430;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = @location_127,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2445;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = @location_127,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2446;
UPDATE inventory_unit SET
  customer_id = 41,
  customer_location_id = @location_115,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2453;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = @location_136,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2456;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2457;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2459;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2460;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2463;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2464;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2465;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2469;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2470;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2472;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = @contract_353,
  area_id = 19,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2473;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_116,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2474;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2475;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2476;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2477;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2480;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2485;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 10600000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2487;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2489;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2491;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2492;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2497;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_354,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2505;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_354,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2510;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_354,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2511;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2512;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = @location_131,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2513;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2528;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2531;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2534;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 25300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2535;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2543;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = @location_140,
  kontrak_id = @contract_355,
  area_id = NULL,
  harga_sewa_bulanan = 15500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2551;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_111,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2560;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_356,
  area_id = 2,
  harga_sewa_bulanan = 14500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2570;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_141,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2571;
UPDATE inventory_unit SET
  customer_id = 238,
  customer_location_id = @location_142,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2572;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = @location_140,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2573;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_122,
  kontrak_id = @contract_117,
  area_id = NULL,
  harga_sewa_bulanan = 30500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2574;

-- Processed 1200/2178 units

UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = @location_140,
  kontrak_id = @contract_355,
  area_id = NULL,
  harga_sewa_bulanan = 15500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2579;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = @location_143,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2580;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2583;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = @location_145,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2584;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_357,
  area_id = 33,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2585;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = @location_133,
  kontrak_id = @contract_351,
  area_id = 19,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2593;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = NULL,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2605;
UPDATE inventory_unit SET
  customer_id = 232,
  customer_location_id = @location_147,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2617;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = @location_148,
  kontrak_id = NULL,
  area_id = 20,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2618;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2622;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2635;
UPDATE inventory_unit SET
  customer_id = 199,
  customer_location_id = @location_134,
  kontrak_id = NULL,
  area_id = 33,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2636;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_111,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2641;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = @location_149,
  kontrak_id = @contract_358,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2643;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = @location_113,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2647;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2648;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2653;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2654;
UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = @location_117,
  kontrak_id = @contract_359,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2655;
UPDATE inventory_unit SET
  customer_id = 9,
  customer_location_id = @location_150,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2656;
UPDATE inventory_unit SET
  customer_id = 94,
  customer_location_id = @location_151,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2664;
UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = @location_117,
  kontrak_id = @contract_345,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2669;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = @location_149,
  kontrak_id = @contract_358,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2680;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_116,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2688;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = @contract_350,
  area_id = 27,
  harga_sewa_bulanan = 70000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2689;
UPDATE inventory_unit SET
  customer_id = 199,
  customer_location_id = @location_134,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2695;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = @location_152,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2725;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = @location_152,
  kontrak_id = @contract_360,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5021;
UPDATE inventory_unit SET
  customer_id = 7,
  customer_location_id = @location_153,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2741;
UPDATE inventory_unit SET
  customer_id = 7,
  customer_location_id = @location_153,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2744;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = @location_113,
  kontrak_id = @contract_361,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2745;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = @location_131,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2748;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_111,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2749;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2750;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_122,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2752;
UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = @location_117,
  kontrak_id = @contract_344,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2763;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = @location_131,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2764;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = @location_154,
  kontrak_id = NULL,
  area_id = 35,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2768;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2772;
UPDATE inventory_unit SET
  customer_id = 9,
  customer_location_id = @location_150,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2773;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2776;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2779;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2793;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2794;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2795;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2796;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2797;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2799;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2800;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2802;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2803;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2804;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5849;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 10500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2807;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 10500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2808;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2809;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2810;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2811;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2812;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2813;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2814;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2815;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2816;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2817;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2818;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2819;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5852;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 2850000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2822;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = @location_131,
  kontrak_id = @contract_363,
  area_id = NULL,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2823;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5853;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = @location_131,
  kontrak_id = @contract_363,
  area_id = NULL,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2825;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2831;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2832;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5855;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2838;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2839;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5856;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5860;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = @location_64,
  kontrak_id = @contract_364,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2866;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 820;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 821;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 824;
UPDATE inventory_unit SET
  customer_id = 177,
  customer_location_id = @location_120,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2875;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = @location_155,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 835;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = NULL,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2883;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = @location_131,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2884;
UPDATE inventory_unit SET
  customer_id = 94,
  customer_location_id = @location_151,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2886;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = @location_124,
  kontrak_id = @contract_365,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2888;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 844;
UPDATE inventory_unit SET
  customer_id = 24,
  customer_location_id = @location_156,
  kontrak_id = NULL,
  area_id = 21,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2905;
UPDATE inventory_unit SET
  customer_id = 49,
  customer_location_id = @location_157,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2910;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2948;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2949;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2950;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5002;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5003;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 9600000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 908;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_366,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5005;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5007;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_106,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5008;

-- Processed 1300/2178 units

UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = @location_131,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2963;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = @location_127,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2964;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = @location_152,
  kontrak_id = @contract_360,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5018;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = @location_152,
  kontrak_id = @contract_360,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5019;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_367,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2972;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_367,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2973;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_367,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2974;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_367,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2975;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_367,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2976;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = NULL,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2977;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_368,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2978;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = @location_152,
  kontrak_id = @contract_360,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5020;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_369,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2980;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_370,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2981;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = @location_124,
  kontrak_id = @contract_371,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 932;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = @location_152,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5022;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = @location_152,
  kontrak_id = @contract_360,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5023;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = @location_152,
  kontrak_id = @contract_360,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5024;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2987;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5036;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2989;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = @location_149,
  kontrak_id = @contract_358,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2990;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5038;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 2850000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2992;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_372,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5040;
UPDATE inventory_unit SET
  customer_id = 148,
  customer_location_id = @location_158,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2998;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = @location_127,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3001;
UPDATE inventory_unit SET
  customer_id = 232,
  customer_location_id = @location_147,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3006;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = @location_131,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3015;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3016;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5072;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5076;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_118,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3031;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = @location_124,
  kontrak_id = @contract_373,
  area_id = 35,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3032;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5084;
UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = @location_117,
  kontrak_id = @contract_345,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3038;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 32150000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3041;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 32150000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3042;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5090;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5091;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5092;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5093;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5094;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5096;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = NULL,
  area_id = 33,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1005;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3055;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3058;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = @location_131,
  kontrak_id = NULL,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3060;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5110;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = @location_155,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1015;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5111;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = @location_159,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5132;
UPDATE inventory_unit SET
  customer_id = 218,
  customer_location_id = @location_160,
  kontrak_id = @contract_374,
  area_id = NULL,
  harga_sewa_bulanan = 23500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5133;
UPDATE inventory_unit SET
  customer_id = 84,
  customer_location_id = @location_161,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3091;
UPDATE inventory_unit SET
  customer_id = 99,
  customer_location_id = @location_119,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3092;
UPDATE inventory_unit SET
  customer_id = 84,
  customer_location_id = @location_161,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3095;
UPDATE inventory_unit SET
  customer_id = 84,
  customer_location_id = @location_161,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3096;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = @location_124,
  kontrak_id = @contract_375,
  area_id = 33,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3101;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = @location_113,
  kontrak_id = @contract_376,
  area_id = NULL,
  harga_sewa_bulanan = 10300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3102;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = @location_113,
  kontrak_id = @contract_377,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3103;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = @location_131,
  kontrak_id = @contract_360,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3104;
UPDATE inventory_unit SET
  customer_id = 199,
  customer_location_id = @location_134,
  kontrak_id = @contract_378,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3105;
UPDATE inventory_unit SET
  customer_id = 84,
  customer_location_id = @location_161,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3106;
UPDATE inventory_unit SET
  customer_id = 199,
  customer_location_id = @location_134,
  kontrak_id = @contract_379,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1059;
UPDATE inventory_unit SET
  customer_id = 2,
  customer_location_id = @location_162,
  kontrak_id = @contract_380,
  area_id = 20,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5150;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = @location_152,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5152;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = @location_113,
  kontrak_id = @contract_381,
  area_id = NULL,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3110;
UPDATE inventory_unit SET
  customer_id = 238,
  customer_location_id = @location_142,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5156;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = @location_124,
  kontrak_id = @contract_382,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5160;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3120;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_383,
  area_id = 33,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5170;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3128;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = @location_163,
  kontrak_id = @contract_384,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3129;
UPDATE inventory_unit SET
  customer_id = 41,
  customer_location_id = @location_115,
  kontrak_id = @contract_385,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3130;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = @location_124,
  kontrak_id = @contract_386,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3131;
UPDATE inventory_unit SET
  customer_id = 41,
  customer_location_id = @location_115,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3132;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_387,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3139;
UPDATE inventory_unit SET
  customer_id = 174,
  customer_location_id = @location_164,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5193;
UPDATE inventory_unit SET
  customer_id = 220,
  customer_location_id = @location_130,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5194;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 25300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3153;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3155;
UPDATE inventory_unit SET
  customer_id = 199,
  customer_location_id = @location_134,
  kontrak_id = @contract_388,
  area_id = 33,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3156;
UPDATE inventory_unit SET
  customer_id = 238,
  customer_location_id = @location_142,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5207;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3160;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3161;
UPDATE inventory_unit SET
  customer_id = 34,
  customer_location_id = @location_128,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3164;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5214;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_118,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3173;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = @location_64,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5221;
UPDATE inventory_unit SET
  customer_id = 98,
  customer_location_id = @location_129,
  kontrak_id = @contract_389,
  area_id = NULL,
  harga_sewa_bulanan = 5500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3175;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_390,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5516;
UPDATE inventory_unit SET
  customer_id = 102,
  customer_location_id = @location_165,
  kontrak_id = NULL,
  area_id = 21,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3178;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = @location_113,
  kontrak_id = @contract_391,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3185;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = @location_113,
  kontrak_id = @contract_392,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3186;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 22800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5234;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 22800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5235;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 22800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5236;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 22800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5237;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_393,
  area_id = 33,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5239;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_394,
  area_id = 13,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5240;

-- Processed 1400/2178 units

UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_395,
  area_id = 33,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5241;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = @location_155,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 11800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5520;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = @location_155,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 11800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5521;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_396,
  area_id = 33,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5251;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = @contract_397,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5252;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_398,
  area_id = 13,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5253;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = NULL,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5254;
UPDATE inventory_unit SET
  customer_id = 84,
  customer_location_id = @location_161,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3207;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3208;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = @contract_397,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5255;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 21500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5256;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3211;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = @location_126,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3214;
UPDATE inventory_unit SET
  customer_id = 126,
  customer_location_id = @location_166,
  kontrak_id = NULL,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3217;
UPDATE inventory_unit SET
  customer_id = 220,
  customer_location_id = @location_130,
  kontrak_id = @contract_228,
  area_id = NULL,
  harga_sewa_bulanan = 16350000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5269;
UPDATE inventory_unit SET
  customer_id = 220,
  customer_location_id = @location_130,
  kontrak_id = @contract_228,
  area_id = NULL,
  harga_sewa_bulanan = 16350000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5270;
UPDATE inventory_unit SET
  customer_id = 220,
  customer_location_id = @location_130,
  kontrak_id = @contract_228,
  area_id = NULL,
  harga_sewa_bulanan = 16350000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5271;
UPDATE inventory_unit SET
  customer_id = 220,
  customer_location_id = @location_130,
  kontrak_id = @contract_399,
  area_id = NULL,
  harga_sewa_bulanan = 10500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5273;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1179;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1180;
UPDATE inventory_unit SET
  customer_id = 29,
  customer_location_id = @location_121,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3233;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = @location_136,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3242;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = NULL,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5292;
UPDATE inventory_unit SET
  customer_id = 41,
  customer_location_id = @location_115,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3249;
UPDATE inventory_unit SET
  customer_id = 98,
  customer_location_id = @location_129,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1203;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = @location_154,
  kontrak_id = NULL,
  area_id = 35,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3254;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = @location_127,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5303;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1208;
UPDATE inventory_unit SET
  customer_id = 203,
  customer_location_id = @location_167,
  kontrak_id = @contract_400,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5304;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = @location_140,
  kontrak_id = @contract_355,
  area_id = NULL,
  harga_sewa_bulanan = 15500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5307;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1214;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = @location_133,
  kontrak_id = @contract_351,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1215;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = @location_163,
  kontrak_id = @contract_129,
  area_id = NULL,
  harga_sewa_bulanan = 5850000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3262;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = @location_155,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1217;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = @location_155,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1218;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3266;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = @location_131,
  kontrak_id = NULL,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3268;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = @location_145,
  kontrak_id = @contract_401,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5319;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = @location_145,
  kontrak_id = @contract_401,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5320;
UPDATE inventory_unit SET
  customer_id = 220,
  customer_location_id = @location_130,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5323;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 21500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5329;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3286;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3287;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3289;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = @location_155,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1247;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = @location_155,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1248;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = @location_155,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1249;
UPDATE inventory_unit SET
  customer_id = 238,
  customer_location_id = @location_142,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3300;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = @location_124,
  kontrak_id = @contract_402,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3301;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3302;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = @contract_403,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3305;
UPDATE inventory_unit SET
  customer_id = 199,
  customer_location_id = @location_134,
  kontrak_id = @contract_404,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1258;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = @contract_405,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3306;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = @contract_405,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3307;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_406,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5357;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = @location_155,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1262;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_407,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5358;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_408,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5359;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = @location_155,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1265;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = @location_155,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1266;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = @location_155,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1267;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 10100000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1268;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_116,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3315;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = @contract_343,
  area_id = NULL,
  harga_sewa_bulanan = 13800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1270;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = @location_124,
  kontrak_id = @contract_409,
  area_id = 33,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3318;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 25300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5360;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 25300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5361;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = @location_131,
  kontrak_id = @contract_360,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3323;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = @location_131,
  kontrak_id = @contract_360,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3324;
UPDATE inventory_unit SET
  customer_id = 49,
  customer_location_id = @location_157,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3327;
UPDATE inventory_unit SET
  customer_id = 99,
  customer_location_id = @location_119,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3328;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = @location_155,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1281;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = @location_155,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1282;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = @location_133,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3329;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = @location_127,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5375;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_410,
  area_id = 13,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5377;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_411,
  area_id = 13,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5378;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = @location_155,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1287;
UPDATE inventory_unit SET
  customer_id = 237,
  customer_location_id = @location_168,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5382;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = @location_145,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5385;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_412,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5387;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = @location_169,
  kontrak_id = @contract_413,
  area_id = NULL,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5388;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5389;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 23800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5390;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = @location_145,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5396;
UPDATE inventory_unit SET
  customer_id = 99,
  customer_location_id = @location_119,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1301;
UPDATE inventory_unit SET
  customer_id = 36,
  customer_location_id = @location_170,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5403;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = @location_140,
  kontrak_id = @contract_355,
  area_id = NULL,
  harga_sewa_bulanan = 15500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5404;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_372,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5407;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = @location_64,
  kontrak_id = @contract_364,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5409;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 26800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5410;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 26800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5411;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5412;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = NULL,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3366;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = @location_124,
  kontrak_id = @contract_414,
  area_id = 33,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3367;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = @location_140,
  kontrak_id = @contract_355,
  area_id = NULL,
  harga_sewa_bulanan = 15500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5414;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = @location_145,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5415;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3371;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = @location_127,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3372;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_415,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5426;

-- Processed 1500/2178 units

UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = @location_117,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1337;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5444;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 22800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5446;
UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = @location_117,
  kontrak_id = @contract_345,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1351;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = @location_127,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3399;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 22800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5447;
UPDATE inventory_unit SET
  customer_id = 33,
  customer_location_id = @location_171,
  kontrak_id = @contract_416,
  area_id = NULL,
  harga_sewa_bulanan = 32500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3402;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 22800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5448;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5451;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5452;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1358;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1359;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1360;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1361;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = @location_127,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3406;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = @location_127,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3407;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = @location_127,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3408;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5453;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5454;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5455;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = 35,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5456;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5457;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = 35,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5458;
UPDATE inventory_unit SET
  customer_id = 208,
  customer_location_id = @location_172,
  kontrak_id = @contract_417,
  area_id = NULL,
  harga_sewa_bulanan = 29000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5462;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 22800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5465;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3421;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_418,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5466;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1381;
UPDATE inventory_unit SET
  customer_id = 174,
  customer_location_id = @location_164,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3429;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3430;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3433;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = @location_155,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1386;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3435;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5484;
UPDATE inventory_unit SET
  customer_id = 238,
  customer_location_id = @location_142,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1389;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5485;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5486;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 26800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5487;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_106,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3441;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3442;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 26800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5488;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 26800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5489;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = @location_173,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1397;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = @contract_419,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5490;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = @contract_420,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5491;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5492;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5493;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5494;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_141,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3451;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5495;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5496;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_141,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3454;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5497;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = 35,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5498;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = @location_155,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1409;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1410;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_421,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5500;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_422,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5503;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3461;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_423,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5504;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_424,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5505;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_425,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5506;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = @location_113,
  kontrak_id = @contract_426,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3466;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = @location_113,
  kontrak_id = @contract_427,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3467;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1420;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1421;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = @location_113,
  kontrak_id = @contract_428,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3468;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3470;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_429,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5514;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = @location_173,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1425;
UPDATE inventory_unit SET
  customer_id = 238,
  customer_location_id = @location_142,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1426;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_430,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5515;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3476;
UPDATE inventory_unit SET
  customer_id = 238,
  customer_location_id = @location_142,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1429;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5519;
UPDATE inventory_unit SET
  customer_id = 238,
  customer_location_id = @location_142,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1431;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3479;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3480;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_431,
  area_id = NULL,
  harga_sewa_bulanan = 7900000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3482;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = @location_133,
  kontrak_id = @contract_351,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3484;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_122,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1437;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3486;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = @location_64,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5532;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = 15,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3488;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3489;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = 35,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3490;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_432,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5533;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = @location_140,
  kontrak_id = @contract_433,
  area_id = NULL,
  harga_sewa_bulanan = 24500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3492;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = @location_140,
  kontrak_id = @contract_433,
  area_id = NULL,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3493;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_432,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5534;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_432,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5535;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_432,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5536;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = @location_113,
  kontrak_id = @contract_434,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3497;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = @contract_397,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5537;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_412,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5538;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = @contract_397,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5540;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3501;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = @contract_397,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5541;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = @contract_397,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5542;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_412,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5550;

-- Processed 1600/2178 units

UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_412,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5551;
UPDATE inventory_unit SET
  customer_id = 99,
  customer_location_id = @location_119,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1458;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_412,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5552;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_435,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5553;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_412,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5554;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_415,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5555;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_415,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5556;
UPDATE inventory_unit SET
  customer_id = 196,
  customer_location_id = @location_174,
  kontrak_id = @contract_436,
  area_id = NULL,
  harga_sewa_bulanan = 29000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5557;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = @contract_397,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5558;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1466;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1467;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1468;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1469;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1470;
UPDATE inventory_unit SET
  customer_id = 131,
  customer_location_id = @location_175,
  kontrak_id = @contract_437,
  area_id = 19,
  harga_sewa_bulanan = 9250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5567;
UPDATE inventory_unit SET
  customer_id = 131,
  customer_location_id = @location_175,
  kontrak_id = @contract_437,
  area_id = 19,
  harga_sewa_bulanan = 9250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5568;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5569;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = @location_176,
  kontrak_id = @contract_438,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5572;
UPDATE inventory_unit SET
  customer_id = 33,
  customer_location_id = @location_171,
  kontrak_id = @contract_416,
  area_id = NULL,
  harga_sewa_bulanan = 32500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3525;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = @location_176,
  kontrak_id = @contract_439,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5573;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = @location_127,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1479;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = @location_176,
  kontrak_id = @contract_439,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5574;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = @location_127,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1481;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = @location_124,
  kontrak_id = @contract_440,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1482;
UPDATE inventory_unit SET
  customer_id = 233,
  customer_location_id = @location_177,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3536;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_367,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5587;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = @location_113,
  kontrak_id = @contract_441,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3540;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_368,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5588;
UPDATE inventory_unit SET
  customer_id = 220,
  customer_location_id = @location_130,
  kontrak_id = @contract_228,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3542;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = @contract_397,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5590;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = @contract_397,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5591;
UPDATE inventory_unit SET
  customer_id = 98,
  customer_location_id = @location_129,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1497;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = @contract_397,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5592;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = @contract_397,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5593;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = @location_127,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1500;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = @location_127,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3549;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = @location_124,
  kontrak_id = @contract_442,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3550;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_116,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5594;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = @location_113,
  kontrak_id = @contract_443,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5597;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = @location_152,
  kontrak_id = @contract_444,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3553;
UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = @location_117,
  kontrak_id = @contract_345,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1506;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = @location_133,
  kontrak_id = @contract_351,
  area_id = NULL,
  harga_sewa_bulanan = 13500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3555;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_445,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3556;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = @location_113,
  kontrak_id = @contract_446,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5598;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = @location_140,
  kontrak_id = @contract_433,
  area_id = NULL,
  harga_sewa_bulanan = 15500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5600;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = @location_140,
  kontrak_id = @contract_433,
  area_id = NULL,
  harga_sewa_bulanan = 15500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5601;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3560;
UPDATE inventory_unit SET
  customer_id = 236,
  customer_location_id = @location_178,
  kontrak_id = @contract_447,
  area_id = 13,
  harga_sewa_bulanan = 9500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5603;
UPDATE inventory_unit SET
  customer_id = 41,
  customer_location_id = @location_115,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3567;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = @contract_350,
  area_id = 27,
  harga_sewa_bulanan = 48500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5621;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = @location_113,
  kontrak_id = @contract_448,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3575;
UPDATE inventory_unit SET
  customer_id = 220,
  customer_location_id = @location_130,
  kontrak_id = @contract_228,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1529;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = @location_131,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5632;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5633;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5639;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5640;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = @location_155,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5641;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = @location_113,
  kontrak_id = @contract_448,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3599;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = @location_145,
  kontrak_id = @contract_449,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5648;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = @location_113,
  kontrak_id = @contract_448,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3601;
UPDATE inventory_unit SET
  customer_id = 41,
  customer_location_id = @location_115,
  kontrak_id = @contract_450,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3602;
UPDATE inventory_unit SET
  customer_id = 41,
  customer_location_id = @location_115,
  kontrak_id = @contract_450,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3603;
UPDATE inventory_unit SET
  customer_id = 41,
  customer_location_id = @location_115,
  kontrak_id = @contract_450,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3604;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = @location_140,
  kontrak_id = @contract_433,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5649;
UPDATE inventory_unit SET
  customer_id = 222,
  customer_location_id = @location_179,
  kontrak_id = @contract_451,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5653;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1559;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_118,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3608;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = @location_133,
  kontrak_id = @contract_351,
  area_id = NULL,
  harga_sewa_bulanan = 14500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5654;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = @location_140,
  kontrak_id = @contract_433,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5660;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = @location_140,
  kontrak_id = @contract_433,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5661;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = @location_113,
  kontrak_id = @contract_448,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3614;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = @location_140,
  kontrak_id = @contract_433,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5662;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = @location_131,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1569;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = @location_133,
  kontrak_id = @contract_351,
  area_id = 19,
  harga_sewa_bulanan = 14700000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3621;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = @location_140,
  kontrak_id = @contract_433,
  area_id = NULL,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5671;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = @location_140,
  kontrak_id = @contract_433,
  area_id = NULL,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5672;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = @location_4,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3626;
UPDATE inventory_unit SET
  customer_id = 118,
  customer_location_id = @location_180,
  kontrak_id = @contract_452,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1580;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = @location_133,
  kontrak_id = @contract_351,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3629;
UPDATE inventory_unit SET
  customer_id = 52,
  customer_location_id = @location_181,
  kontrak_id = @contract_453,
  area_id = NULL,
  harga_sewa_bulanan = 6750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3631;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = @location_148,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3635;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = @contract_397,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5683;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = @location_113,
  kontrak_id = @contract_448,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3637;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = NULL,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5685;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = NULL,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5686;
UPDATE inventory_unit SET
  customer_id = 196,
  customer_location_id = @location_174,
  kontrak_id = @contract_454,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3640;
UPDATE inventory_unit SET
  customer_id = 198,
  customer_location_id = @location_182,
  kontrak_id = @contract_455,
  area_id = 17,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3643;
UPDATE inventory_unit SET
  customer_id = 2,
  customer_location_id = @location_162,
  kontrak_id = NULL,
  area_id = 20,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3644;
UPDATE inventory_unit SET
  customer_id = 220,
  customer_location_id = @location_130,
  kontrak_id = @contract_228,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3647;
UPDATE inventory_unit SET
  customer_id = 41,
  customer_location_id = @location_115,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3649;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = @location_152,
  kontrak_id = @contract_456,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5697;
UPDATE inventory_unit SET
  customer_id = 60,
  customer_location_id = @location_183,
  kontrak_id = @contract_457,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5698;
UPDATE inventory_unit SET
  customer_id = 60,
  customer_location_id = @location_183,
  kontrak_id = @contract_458,
  area_id = NULL,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5701;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5702;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = @location_113,
  kontrak_id = @contract_459,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3655;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = @location_152,
  kontrak_id = @contract_456,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5705;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = @location_152,
  kontrak_id = @contract_456,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5706;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = @location_152,
  kontrak_id = @contract_456,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5707;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = @location_152,
  kontrak_id = @contract_456,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5708;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = @location_152,
  kontrak_id = @contract_456,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5709;

-- Processed 1700/2178 units

UPDATE inventory_unit SET
  customer_id = 29,
  customer_location_id = @location_121,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3662;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = @location_152,
  kontrak_id = @contract_456,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5710;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3664;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = @location_152,
  kontrak_id = @contract_456,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5711;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1618;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = @location_152,
  kontrak_id = @contract_456,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5712;
UPDATE inventory_unit SET
  customer_id = 196,
  customer_location_id = @location_174,
  kontrak_id = @contract_460,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3668;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = @location_152,
  kontrak_id = @contract_456,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5715;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = @location_152,
  kontrak_id = @contract_456,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5716;
UPDATE inventory_unit SET
  customer_id = 198,
  customer_location_id = @location_182,
  kontrak_id = @contract_455,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3671;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = @location_152,
  kontrak_id = @contract_456,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5717;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = @location_152,
  kontrak_id = @contract_456,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5720;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_116,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3674;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = @location_145,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5721;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = @location_145,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5726;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = @location_140,
  kontrak_id = @contract_355,
  area_id = NULL,
  harga_sewa_bulanan = 15500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5727;
UPDATE inventory_unit SET
  customer_id = 60,
  customer_location_id = @location_183,
  kontrak_id = NULL,
  area_id = 2,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5728;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = @location_145,
  kontrak_id = @contract_461,
  area_id = NULL,
  harga_sewa_bulanan = 4700000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5730;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = @location_145,
  kontrak_id = @contract_461,
  area_id = NULL,
  harga_sewa_bulanan = 4700000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5731;
UPDATE inventory_unit SET
  customer_id = 98,
  customer_location_id = @location_129,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1636;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = @location_145,
  kontrak_id = @contract_461,
  area_id = NULL,
  harga_sewa_bulanan = 4700000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5732;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = @location_124,
  kontrak_id = @contract_462,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5733;
UPDATE inventory_unit SET
  customer_id = 203,
  customer_location_id = @location_167,
  kontrak_id = @contract_463,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5735;
UPDATE inventory_unit SET
  customer_id = 83,
  customer_location_id = @location_184,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3689;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5750;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_354,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3705;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1658;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = @location_124,
  kontrak_id = @contract_464,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1661;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = @location_124,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3710;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = @location_133,
  kontrak_id = @contract_351,
  area_id = 19,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3714;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = @location_145,
  kontrak_id = @contract_465,
  area_id = NULL,
  harga_sewa_bulanan = 4250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5762;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = @location_64,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3716;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_431,
  area_id = NULL,
  harga_sewa_bulanan = 8100000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1671;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1678;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 6900000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5777;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5783;
UPDATE inventory_unit SET
  customer_id = 239,
  customer_location_id = @location_185,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5784;
UPDATE inventory_unit SET
  customer_id = 239,
  customer_location_id = @location_185,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5785;
UPDATE inventory_unit SET
  customer_id = 196,
  customer_location_id = @location_174,
  kontrak_id = @contract_466,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3738;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = NULL,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5787;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = NULL,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5788;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = NULL,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5789;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = @location_132,
  kontrak_id = NULL,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5790;
UPDATE inventory_unit SET
  customer_id = 231,
  customer_location_id = @location_186,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3746;
UPDATE inventory_unit SET
  customer_id = 60,
  customer_location_id = @location_183,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5795;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_467,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5796;
UPDATE inventory_unit SET
  customer_id = 231,
  customer_location_id = @location_186,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3751;
UPDATE inventory_unit SET
  customer_id = 73,
  customer_location_id = @location_187,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1716;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5815;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_468,
  area_id = 33,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5817;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = @location_145,
  kontrak_id = @contract_469,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5818;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = @location_145,
  kontrak_id = @contract_469,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5819;
UPDATE inventory_unit SET
  customer_id = 83,
  customer_location_id = @location_184,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3773;
UPDATE inventory_unit SET
  customer_id = 83,
  customer_location_id = @location_184,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3774;
UPDATE inventory_unit SET
  customer_id = 83,
  customer_location_id = @location_184,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3775;
UPDATE inventory_unit SET
  customer_id = 2,
  customer_location_id = @location_162,
  kontrak_id = @contract_470,
  area_id = 20,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3776;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5824;
UPDATE inventory_unit SET
  customer_id = 196,
  customer_location_id = @location_174,
  kontrak_id = @contract_460,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3778;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = @location_148,
  kontrak_id = NULL,
  area_id = 20,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1731;
UPDATE inventory_unit SET
  customer_id = 198,
  customer_location_id = @location_182,
  kontrak_id = @contract_471,
  area_id = 17,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3780;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5825;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5826;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5828;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5829;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5830;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5831;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5832;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5833;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3789;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5834;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5835;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5836;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5837;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5838;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5839;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5840;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5841;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5842;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5843;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5844;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5845;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5846;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5847;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5848;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = @location_133,
  kontrak_id = @contract_351,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3805;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = @location_133,
  kontrak_id = @contract_351,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3806;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = @location_133,
  kontrak_id = @contract_351,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3807;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = @location_133,
  kontrak_id = @contract_351,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3808;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = @location_133,
  kontrak_id = @contract_351,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3809;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = @location_133,
  kontrak_id = @contract_351,
  area_id = 19,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3810;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5850;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5851;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3813;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3814;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5854;
UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = @location_117,
  kontrak_id = @contract_345,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1768;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3816;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5857;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5858;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5859;

-- Processed 1800/2178 units

UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3821;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5861;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5862;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5863;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5864;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5867;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5868;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5869;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3829;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5870;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5871;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5872;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5873;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5874;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5875;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5876;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5877;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5878;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 6900000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3839;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5879;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5880;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5881;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5883;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5884;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = @location_143,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5885;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5886;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5887;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5888;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5889;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = @location_145,
  kontrak_id = @contract_472,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5891;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = @location_140,
  kontrak_id = @contract_355,
  area_id = NULL,
  harga_sewa_bulanan = 15500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5892;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = @location_113,
  kontrak_id = @contract_473,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5896;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = @location_113,
  kontrak_id = @contract_474,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5897;
UPDATE inventory_unit SET
  customer_id = 199,
  customer_location_id = @location_134,
  kontrak_id = @contract_475,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1807;
UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = @location_117,
  kontrak_id = @contract_345,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1809;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = @location_131,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1812;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = NULL,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1814;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = @location_154,
  kontrak_id = NULL,
  area_id = 35,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3865;
UPDATE inventory_unit SET
  customer_id = 60,
  customer_location_id = @location_183,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5917;
UPDATE inventory_unit SET
  customer_id = 230,
  customer_location_id = @location_188,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1824;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_111,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1827;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = @location_124,
  kontrak_id = @contract_476,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3876;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3877;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = @location_149,
  kontrak_id = @contract_477,
  area_id = NULL,
  harga_sewa_bulanan = 9500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3881;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3883;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3885;
UPDATE inventory_unit SET
  customer_id = 207,
  customer_location_id = @location_189,
  kontrak_id = @contract_354,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3886;
UPDATE inventory_unit SET
  customer_id = 142,
  customer_location_id = @location_190,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3887;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_478,
  area_id = 17,
  harga_sewa_bulanan = 12250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3889;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_479,
  area_id = 2,
  harga_sewa_bulanan = 14500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3890;
UPDATE inventory_unit SET
  customer_id = 199,
  customer_location_id = @location_134,
  kontrak_id = @contract_352,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1843;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = @location_152,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1847;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_480,
  area_id = 17,
  harga_sewa_bulanan = 12250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3895;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 11800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3899;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = @location_124,
  kontrak_id = @contract_481,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3900;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = @location_133,
  kontrak_id = @contract_351,
  area_id = 19,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3901;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_144,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3910;
UPDATE inventory_unit SET
  customer_id = 140,
  customer_location_id = @location_191,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1868;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5965;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5966;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5967;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5968;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5969;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5970;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5971;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_482,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3925;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_482,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3926;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5973;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = @location_64,
  kontrak_id = @contract_364,
  area_id = NULL,
  harga_sewa_bulanan = 9450000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3928;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_483,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3929;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5974;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5975;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5976;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_482,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3933;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5977;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3935;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = @contract_482,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3936;
UPDATE inventory_unit SET
  customer_id = 198,
  customer_location_id = @location_182,
  kontrak_id = @contract_484,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3937;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = @location_140,
  kontrak_id = @contract_485,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5978;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 27500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5986;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = @location_145,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5987;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = @location_145,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5988;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = @location_145,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5991;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = @location_131,
  kontrak_id = @contract_363,
  area_id = NULL,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3945;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5993;
UPDATE inventory_unit SET
  customer_id = 166,
  customer_location_id = @location_123,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1900;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = @location_140,
  kontrak_id = @contract_485,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5997;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_116,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3953;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3957;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_342,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3958;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = @contract_486,
  area_id = 17,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3959;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_192,
  kontrak_id = @contract_327,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6005;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_192,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6008;
UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = @location_117,
  kontrak_id = @contract_345,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1914;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = @location_145,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6012;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_487,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6016;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_487,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6017;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_192,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6019;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_192,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6021;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = @location_152,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6022;

-- Processed 1900/2178 units

UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = NULL,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1927;
UPDATE inventory_unit SET
  customer_id = 41,
  customer_location_id = @location_115,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1929;
UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = @location_117,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3978;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3979;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = @location_145,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6028;
UPDATE inventory_unit SET
  customer_id = 129,
  customer_location_id = @location_193,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1933;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = @location_64,
  kontrak_id = @contract_110,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1934;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3983;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = @location_133,
  kontrak_id = @contract_351,
  area_id = 19,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3984;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = @location_131,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1937;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = @location_138,
  kontrak_id = @contract_488,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3987;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = @location_145,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6038;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = 35,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6039;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = @location_112,
  kontrak_id = NULL,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6040;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = @location_131,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1945;
UPDATE inventory_unit SET
  customer_id = 14,
  customer_location_id = @location_52,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6041;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6042;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = @location_137,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6043;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = @location_124,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 9300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1950;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = @location_114,
  kontrak_id = @contract_362,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1951;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = @location_107,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1953;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = @location_124,
  kontrak_id = @contract_489,
  area_id = 26,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1955;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6051;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6052;
UPDATE inventory_unit SET
  customer_id = 2,
  customer_location_id = @location_162,
  kontrak_id = NULL,
  area_id = 20,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6054;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = @location_146,
  kontrak_id = NULL,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1963;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6062;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6064;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = @location_154,
  kontrak_id = NULL,
  area_id = 35,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1972;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6075;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = @location_139,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6076;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = @location_131,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1983;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = @location_131,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1984;
UPDATE inventory_unit SET
  customer_id = 2,
  customer_location_id = @location_162,
  kontrak_id = @contract_490,
  area_id = 20,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6085;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = @location_131,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1990;
UPDATE inventory_unit SET
  customer_id = 99,
  customer_location_id = @location_119,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2019;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = @location_64,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2021;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = @location_127,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2034;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = @location_135,
  kontrak_id = @contract_353,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2044;
UPDATE inventory_unit SET
  customer_id = 27,
  customer_location_id = 88,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 6043733.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_45,
  kontrak_id = @contract_131,
  area_id = 26,
  harga_sewa_bulanan = 3170000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = @contract_491,
  area_id = NULL,
  harga_sewa_bulanan = 13950000.0,
  on_hire_date = '2025-05-15',
  rate_changed_at = NOW()
WHERE no_unit = 5638;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5904;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_492,
  area_id = NULL,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3595;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-03-16',
  rate_changed_at = NOW()
WHERE no_unit = 3084;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 29000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3596;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 29000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3597;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_493,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-03-20',
  rate_changed_at = NOW()
WHERE no_unit = 3087;
UPDATE inventory_unit SET
  customer_id = 152,
  customer_location_id = 392,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 16;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-05-16',
  rate_changed_at = NOW()
WHERE no_unit = 3088;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3089;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 47500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3600;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5905;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5906;
UPDATE inventory_unit SET
  customer_id = 191,
  customer_location_id = 436,
  kontrak_id = @contract_494,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5655;
UPDATE inventory_unit SET
  customer_id = 191,
  customer_location_id = 436,
  kontrak_id = @contract_494,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5656;
UPDATE inventory_unit SET
  customer_id = 148,
  customer_location_id = 386,
  kontrak_id = @contract_495,
  area_id = NULL,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 26;
UPDATE inventory_unit SET
  customer_id = 191,
  customer_location_id = 436,
  kontrak_id = @contract_494,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5658;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5908;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_496,
  area_id = NULL,
  harga_sewa_bulanan = 31700000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3616;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5909;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5910;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 251,
  kontrak_id = @contract_33,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2024-06-13',
  rate_changed_at = NOW()
WHERE no_unit = 3627;
UPDATE inventory_unit SET
  customer_id = 19,
  customer_location_id = @location_194,
  kontrak_id = @contract_497,
  area_id = NULL,
  harga_sewa_bulanan = 13500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5168;
UPDATE inventory_unit SET
  customer_id = 197,
  customer_location_id = @location_195,
  kontrak_id = @contract_242,
  area_id = NULL,
  harga_sewa_bulanan = 32500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5681;
UPDATE inventory_unit SET
  customer_id = 197,
  customer_location_id = @location_195,
  kontrak_id = @contract_242,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3638;
UPDATE inventory_unit SET
  customer_id = 3,
  customer_location_id = @location_196,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39000000.0,
  on_hire_date = '2025-06-20',
  rate_changed_at = NOW()
WHERE no_unit = 5687;
UPDATE inventory_unit SET
  customer_id = 3,
  customer_location_id = @location_196,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39000000.0,
  on_hire_date = '2025-06-20',
  rate_changed_at = NOW()
WHERE no_unit = 5688;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = @location_99,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 16300000.0,
  on_hire_date = '2025-05-03',
  rate_changed_at = NOW()
WHERE no_unit = 3641;
UPDATE inventory_unit SET
  customer_id = 3,
  customer_location_id = @location_196,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39000000.0,
  on_hire_date = '2025-06-20',
  rate_changed_at = NOW()
WHERE no_unit = 5689;
UPDATE inventory_unit SET
  customer_id = 3,
  customer_location_id = @location_196,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39000000.0,
  on_hire_date = '2025-06-20',
  rate_changed_at = NOW()
WHERE no_unit = 5690;
UPDATE inventory_unit SET
  customer_id = 3,
  customer_location_id = @location_196,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39000000.0,
  on_hire_date = '2025-06-20',
  rate_changed_at = NOW()
WHERE no_unit = 5691;
UPDATE inventory_unit SET
  customer_id = 3,
  customer_location_id = @location_196,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39000000.0,
  on_hire_date = '2025-06-20',
  rate_changed_at = NOW()
WHERE no_unit = 5692;
UPDATE inventory_unit SET
  customer_id = 3,
  customer_location_id = @location_196,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39000000.0,
  on_hire_date = '2025-06-20',
  rate_changed_at = NOW()
WHERE no_unit = 5693;
UPDATE inventory_unit SET
  customer_id = 3,
  customer_location_id = @location_196,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39000000.0,
  on_hire_date = '2025-06-20',
  rate_changed_at = NOW()
WHERE no_unit = 5694;
UPDATE inventory_unit SET
  customer_id = 3,
  customer_location_id = @location_196,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39000000.0,
  on_hire_date = '2025-06-20',
  rate_changed_at = NOW()
WHERE no_unit = 5695;
UPDATE inventory_unit SET
  customer_id = 3,
  customer_location_id = @location_196,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39000000.0,
  on_hire_date = '2025-06-20',
  rate_changed_at = NOW()
WHERE no_unit = 5696;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_498,
  area_id = NULL,
  harga_sewa_bulanan = 68900000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5188;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 20564000.0,
  on_hire_date = '2023-01-26',
  rate_changed_at = NOW()
WHERE no_unit = 3142;
UPDATE inventory_unit SET
  customer_id = 126,
  customer_location_id = 337,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-07-25',
  rate_changed_at = NOW()
WHERE no_unit = 5704;
UPDATE inventory_unit SET
  customer_id = 240,
  customer_location_id = @location_197,
  kontrak_id = @contract_499,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3158;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_500,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3159;
UPDATE inventory_unit SET
  customer_id = 157,
  customer_location_id = 398,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2026-01-21',
  rate_changed_at = NOW()
WHERE no_unit = 3670;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_501,
  area_id = NULL,
  harga_sewa_bulanan = 40300000.0,
  on_hire_date = '2022-12-12',
  rate_changed_at = NOW()
WHERE no_unit = 3166;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 40300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3167;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 40300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3168;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = '2022-10-26',
  rate_changed_at = NOW()
WHERE no_unit = 3169;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5922;
UPDATE inventory_unit SET
  customer_id = 110,
  customer_location_id = 313,
  kontrak_id = @contract_316,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2026-02-02',
  rate_changed_at = NOW()
WHERE no_unit = 3177;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5923;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = @contract_502,
  area_id = NULL,
  harga_sewa_bulanan = 17600000.0,
  on_hire_date = '2025-08-08',
  rate_changed_at = NOW()
WHERE no_unit = 5740;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = @contract_503,
  area_id = NULL,
  harga_sewa_bulanan = 17600000.0,
  on_hire_date = '2025-08-08',
  rate_changed_at = NOW()
WHERE no_unit = 5741;
UPDATE inventory_unit SET
  customer_id = 94,
  customer_location_id = 275,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2158;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 40300000.0,
  on_hire_date = '2022-10-26',
  rate_changed_at = NOW()
WHERE no_unit = 3183;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3184;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = @contract_504,
  area_id = NULL,
  harga_sewa_bulanan = 17600000.0,
  on_hire_date = '2025-08-08',
  rate_changed_at = NOW()
WHERE no_unit = 5742;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = @contract_505,
  area_id = NULL,
  harga_sewa_bulanan = 17600000.0,
  on_hire_date = '2025-08-08',
  rate_changed_at = NOW()
WHERE no_unit = 5743;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = '2022-10-26',
  rate_changed_at = NOW()
WHERE no_unit = 3187;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = @contract_506,
  area_id = NULL,
  harga_sewa_bulanan = 17600000.0,
  on_hire_date = '2025-08-08',
  rate_changed_at = NOW()
WHERE no_unit = 5744;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 40300000.0,
  on_hire_date = '2022-12-12',
  rate_changed_at = NOW()
WHERE no_unit = 3189;

-- Processed 2000/2178 units

UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3190;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39300000.0,
  on_hire_date = '2022-10-20',
  rate_changed_at = NOW()
WHERE no_unit = 3191;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39300000.0,
  on_hire_date = '2022-10-20',
  rate_changed_at = NOW()
WHERE no_unit = 3192;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_496,
  area_id = NULL,
  harga_sewa_bulanan = 20900000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2681;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 47500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2682;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_496,
  area_id = NULL,
  harga_sewa_bulanan = 20900000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2683;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5927;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5928;
UPDATE inventory_unit SET
  customer_id = 241,
  customer_location_id = @location_198,
  kontrak_id = @contract_507,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = '2026-01-21',
  rate_changed_at = NOW()
WHERE no_unit = 3209;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 56500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3210;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5930;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5931;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 237,
  kontrak_id = @contract_122,
  area_id = NULL,
  harga_sewa_bulanan = 12900000.0,
  on_hire_date = '2022-03-04',
  rate_changed_at = NOW()
WHERE no_unit = 3219;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 40300000.0,
  on_hire_date = '2022-11-25',
  rate_changed_at = NOW()
WHERE no_unit = 3222;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3223;
UPDATE inventory_unit SET
  customer_id = 142,
  customer_location_id = 367,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2200;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39300000.0,
  on_hire_date = '2022-11-16',
  rate_changed_at = NOW()
WHERE no_unit = 3224;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3225;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39300000.0,
  on_hire_date = '2022-11-16',
  rate_changed_at = NOW()
WHERE no_unit = 3227;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 42400000.0,
  on_hire_date = '2023-01-26',
  rate_changed_at = NOW()
WHERE no_unit = 3228;
UPDATE inventory_unit SET
  customer_id = 240,
  customer_location_id = @location_197,
  kontrak_id = @contract_508,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3736;
UPDATE inventory_unit SET
  customer_id = 126,
  customer_location_id = 337,
  kontrak_id = @contract_509,
  area_id = NULL,
  harga_sewa_bulanan = 17500000.0,
  on_hire_date = '2026-01-27',
  rate_changed_at = NOW()
WHERE no_unit = 3743;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = '2022-11-24',
  rate_changed_at = NOW()
WHERE no_unit = 3236;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = '2023-01-12',
  rate_changed_at = NOW()
WHERE no_unit = 3239;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = '2022-11-25',
  rate_changed_at = NOW()
WHERE no_unit = 3241;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = '2022-11-24',
  rate_changed_at = NOW()
WHERE no_unit = 3244;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = '2022-11-25',
  rate_changed_at = NOW()
WHERE no_unit = 3246;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = '2022-11-24',
  rate_changed_at = NOW()
WHERE no_unit = 3248;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3250;
UPDATE inventory_unit SET
  customer_id = 104,
  customer_location_id = 305,
  kontrak_id = @contract_510,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-12-15',
  rate_changed_at = NOW()
WHERE no_unit = 3770;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 56500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3271;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = '2022-12-12',
  rate_changed_at = NOW()
WHERE no_unit = 3272;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = '2022-12-12',
  rate_changed_at = NOW()
WHERE no_unit = 3273;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39300000.0,
  on_hire_date = '2022-12-12',
  rate_changed_at = NOW()
WHERE no_unit = 3274;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3275;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_511,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = '2022-12-21',
  rate_changed_at = NOW()
WHERE no_unit = 3277;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = '2022-12-21',
  rate_changed_at = NOW()
WHERE no_unit = 3278;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = '2022-12-21',
  rate_changed_at = NOW()
WHERE no_unit = 3279;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = '2023-01-12',
  rate_changed_at = NOW()
WHERE no_unit = 3280;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = '2022-12-26',
  rate_changed_at = NOW()
WHERE no_unit = 3281;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39300000.0,
  on_hire_date = '2022-12-26',
  rate_changed_at = NOW()
WHERE no_unit = 3282;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 27037500.0,
  on_hire_date = '2023-01-12',
  rate_changed_at = NOW()
WHERE no_unit = 3283;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3284;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = '2022-12-26',
  rate_changed_at = NOW()
WHERE no_unit = 3285;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = NULL,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3791;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_512,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = '2023-01-26',
  rate_changed_at = NOW()
WHERE no_unit = 3290;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = @contract_513,
  area_id = NULL,
  harga_sewa_bulanan = 17600000.0,
  on_hire_date = '2025-08-08',
  rate_changed_at = NOW()
WHERE no_unit = 5745;
UPDATE inventory_unit SET
  customer_id = 129,
  customer_location_id = 342,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2022-01-21',
  rate_changed_at = NOW()
WHERE no_unit = 1774;
UPDATE inventory_unit SET
  customer_id = 23,
  customer_location_id = 82,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-12-02',
  rate_changed_at = NOW()
WHERE no_unit = 6055;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 56500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3332;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 54500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3333;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 56500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3334;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 56500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3335;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 56500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3336;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 56500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3337;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_514,
  area_id = NULL,
  harga_sewa_bulanan = 60000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3338;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5898;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 56500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3340;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 56500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3341;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 56500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3342;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 56500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3343;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 56500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3344;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5899;
UPDATE inventory_unit SET
  customer_id = 49,
  customer_location_id = 163,
  kontrak_id = @contract_515,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2025-05-14',
  rate_changed_at = NOW()
WHERE no_unit = 3858;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_516,
  area_id = NULL,
  harga_sewa_bulanan = 58000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5395;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5901;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 54500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3349;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 54500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3350;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 54500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3351;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5903;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 56500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3353;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 419,
  kontrak_id = @contract_35,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2842;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_514,
  area_id = NULL,
  harga_sewa_bulanan = 60000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3354;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_517,
  area_id = NULL,
  harga_sewa_bulanan = 60000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3355;
UPDATE inventory_unit SET
  customer_id = 94,
  customer_location_id = 275,
  kontrak_id = @contract_6,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2022-01-18',
  rate_changed_at = NOW()
WHERE no_unit = 2845;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_517,
  area_id = NULL,
  harga_sewa_bulanan = 60000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3356;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_517,
  area_id = NULL,
  harga_sewa_bulanan = 60000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3357;
UPDATE inventory_unit SET
  customer_id = 61,
  customer_location_id = @location_199,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 16000000.0,
  on_hire_date = '2025-12-15',
  rate_changed_at = NOW()
WHERE no_unit = 2848;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_514,
  area_id = NULL,
  harga_sewa_bulanan = 60000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3359;
UPDATE inventory_unit SET
  customer_id = 3,
  customer_location_id = @location_196,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 45000000.0,
  on_hire_date = '2025-09-02',
  rate_changed_at = NOW()
WHERE no_unit = 5914;
UPDATE inventory_unit SET
  customer_id = 3,
  customer_location_id = @location_196,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 45000000.0,
  on_hire_date = '2025-09-02',
  rate_changed_at = NOW()
WHERE no_unit = 5915;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5918;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = @contract_518,
  area_id = NULL,
  harga_sewa_bulanan = 13950000.0,
  on_hire_date = '2025-01-20',
  rate_changed_at = NOW()
WHERE no_unit = 5413;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5919;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5920;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5921;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = @contract_519,
  area_id = NULL,
  harga_sewa_bulanan = 13950000.0,
  on_hire_date = '2025-01-20',
  rate_changed_at = NOW()
WHERE no_unit = 5417;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 18000000.0,
  on_hire_date = '2026-01-23',
  rate_changed_at = NOW()
WHERE no_unit = 3882;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = @contract_520,
  area_id = NULL,
  harga_sewa_bulanan = 13950000.0,
  on_hire_date = '2025-04-22',
  rate_changed_at = NOW()
WHERE no_unit = 5418;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = @contract_519,
  area_id = NULL,
  harga_sewa_bulanan = 13950000.0,
  on_hire_date = '2025-01-20',
  rate_changed_at = NOW()
WHERE no_unit = 5419;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5924;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5925;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5926;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = @contract_518,
  area_id = NULL,
  harga_sewa_bulanan = 12950000.0,
  on_hire_date = '2025-01-20',
  rate_changed_at = NOW()
WHERE no_unit = 5424;
UPDATE inventory_unit SET
  customer_id = 98,
  customer_location_id = 285,
  kontrak_id = @contract_389,
  area_id = NULL,
  harga_sewa_bulanan = 5500000.0,
  on_hire_date = '2020-09-29',
  rate_changed_at = NOW()
WHERE no_unit = 2865;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5929;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 18000000.0,
  on_hire_date = '2026-01-23',
  rate_changed_at = NOW()
WHERE no_unit = 3891;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = @contract_518,
  area_id = NULL,
  harga_sewa_bulanan = 12950000.0,
  on_hire_date = '2025-01-20',
  rate_changed_at = NOW()
WHERE no_unit = 5427;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5932;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5934;

-- Processed 2100/2178 units

UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5935;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5936;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5937;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5938;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5939;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5940;
UPDATE inventory_unit SET
  customer_id = 23,
  customer_location_id = 82,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-10-17',
  rate_changed_at = NOW()
WHERE no_unit = 5941;
UPDATE inventory_unit SET
  customer_id = 23,
  customer_location_id = 82,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-10-17',
  rate_changed_at = NOW()
WHERE no_unit = 5942;
UPDATE inventory_unit SET
  customer_id = 23,
  customer_location_id = 82,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-10-17',
  rate_changed_at = NOW()
WHERE no_unit = 5943;
UPDATE inventory_unit SET
  customer_id = 23,
  customer_location_id = 82,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-10-17',
  rate_changed_at = NOW()
WHERE no_unit = 5944;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 47500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2381;
UPDATE inventory_unit SET
  customer_id = 23,
  customer_location_id = 82,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-10-17',
  rate_changed_at = NOW()
WHERE no_unit = 5979;
UPDATE inventory_unit SET
  customer_id = 23,
  customer_location_id = 82,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-10-17',
  rate_changed_at = NOW()
WHERE no_unit = 5980;
UPDATE inventory_unit SET
  customer_id = 23,
  customer_location_id = 82,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-10-17',
  rate_changed_at = NOW()
WHERE no_unit = 5981;
UPDATE inventory_unit SET
  customer_id = 23,
  customer_location_id = 82,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-10-17',
  rate_changed_at = NOW()
WHERE no_unit = 5982;
UPDATE inventory_unit SET
  customer_id = 23,
  customer_location_id = 82,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-10-17',
  rate_changed_at = NOW()
WHERE no_unit = 5983;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3424;
UPDATE inventory_unit SET
  customer_id = 23,
  customer_location_id = 82,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-10-17',
  rate_changed_at = NOW()
WHERE no_unit = 5984;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_496,
  area_id = NULL,
  harga_sewa_bulanan = 31700000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2916;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3941;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 29500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2919;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 29500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2924;
UPDATE inventory_unit SET
  customer_id = 158,
  customer_location_id = @location_200,
  kontrak_id = @contract_521,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = '2026-01-29',
  rate_changed_at = NOW()
WHERE no_unit = 2928;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 47500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2929;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 16800000.0,
  on_hire_date = '2021-08-28',
  rate_changed_at = NOW()
WHERE no_unit = 2937;
UPDATE inventory_unit SET
  customer_id = 64,
  customer_location_id = @location_6,
  kontrak_id = @contract_108,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2026-01-27',
  rate_changed_at = NOW()
WHERE no_unit = 6010;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 16800000.0,
  on_hire_date = '2021-08-28',
  rate_changed_at = NOW()
WHERE no_unit = 2941;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 16800000.0,
  on_hire_date = '2021-08-28',
  rate_changed_at = NOW()
WHERE no_unit = 2942;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = @contract_81,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3453;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = @contract_518,
  area_id = NULL,
  harga_sewa_bulanan = 13950000.0,
  on_hire_date = '2025-01-20',
  rate_changed_at = NOW()
WHERE no_unit = 3965;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = @contract_518,
  area_id = NULL,
  harga_sewa_bulanan = 12950000.0,
  on_hire_date = '2025-01-17',
  rate_changed_at = NOW()
WHERE no_unit = 5507;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = @contract_519,
  area_id = NULL,
  harga_sewa_bulanan = 12950000.0,
  on_hire_date = '2025-01-17',
  rate_changed_at = NOW()
WHERE no_unit = 5508;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_16,
  kontrak_id = @contract_157,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3464;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-03-05',
  rate_changed_at = NOW()
WHERE no_unit = 5513;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2955;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_522,
  area_id = NULL,
  harga_sewa_bulanan = 58000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5517;
UPDATE inventory_unit SET
  customer_id = 63,
  customer_location_id = @location_201,
  kontrak_id = @contract_523,
  area_id = NULL,
  harga_sewa_bulanan = 52781040.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2447;
UPDATE inventory_unit SET
  customer_id = 192,
  customer_location_id = 437,
  kontrak_id = @contract_524,
  area_id = NULL,
  harga_sewa_bulanan = 52000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5522;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-02-27',
  rate_changed_at = NOW()
WHERE no_unit = 5524;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2021-10-07',
  rate_changed_at = NOW()
WHERE no_unit = 5525;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2022-02-11',
  rate_changed_at = NOW()
WHERE no_unit = 5526;
UPDATE inventory_unit SET
  customer_id = 152,
  customer_location_id = 392,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-07-29',
  rate_changed_at = NOW()
WHERE no_unit = 5015;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2021-10-07',
  rate_changed_at = NOW()
WHERE no_unit = 5527;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_8,
  kontrak_id = @contract_214,
  area_id = 26,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3481;
UPDATE inventory_unit SET
  customer_id = 106,
  customer_location_id = 309,
  kontrak_id = @contract_525,
  area_id = NULL,
  harga_sewa_bulanan = 22000000.0,
  on_hire_date = '2026-01-27',
  rate_changed_at = NOW()
WHERE no_unit = 5531;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_222,
  area_id = NULL,
  harga_sewa_bulanan = 42400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3495;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_526,
  area_id = NULL,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3496;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-03-06',
  rate_changed_at = NOW()
WHERE no_unit = 5543;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_527,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = '2023-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 3498;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_528,
  area_id = NULL,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 3499;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_529,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3500;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2022-02-11',
  rate_changed_at = NOW()
WHERE no_unit = 5544;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2022-02-11',
  rate_changed_at = NOW()
WHERE no_unit = 5545;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-03-13',
  rate_changed_at = NOW()
WHERE no_unit = 5546;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-03-13',
  rate_changed_at = NOW()
WHERE no_unit = 5547;
UPDATE inventory_unit SET
  customer_id = 23,
  customer_location_id = 82,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-12-02',
  rate_changed_at = NOW()
WHERE no_unit = 6056;
UPDATE inventory_unit SET
  customer_id = 23,
  customer_location_id = 82,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-12-02',
  rate_changed_at = NOW()
WHERE no_unit = 6057;
UPDATE inventory_unit SET
  customer_id = 23,
  customer_location_id = 82,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-12-02',
  rate_changed_at = NOW()
WHERE no_unit = 6058;
UPDATE inventory_unit SET
  customer_id = 23,
  customer_location_id = 82,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-12-02',
  rate_changed_at = NOW()
WHERE no_unit = 6059;
UPDATE inventory_unit SET
  customer_id = 23,
  customer_location_id = 82,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-12-02',
  rate_changed_at = NOW()
WHERE no_unit = 6060;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = '2026-01-23',
  rate_changed_at = NOW()
WHERE no_unit = 6067;
UPDATE inventory_unit SET
  customer_id = 114,
  customer_location_id = 318,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = '2023-02-02',
  rate_changed_at = NOW()
WHERE no_unit = 1975;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 348,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2025-02-22',
  rate_changed_at = NOW()
WHERE no_unit = 5559;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 347,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2025-02-22',
  rate_changed_at = NOW()
WHERE no_unit = 5560;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 349,
  kontrak_id = @contract_25,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2025-02-25',
  rate_changed_at = NOW()
WHERE no_unit = 5561;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = @contract_530,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = '2026-01-23',
  rate_changed_at = NOW()
WHERE no_unit = 2494;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = '2026-01-23',
  rate_changed_at = NOW()
WHERE no_unit = 2496;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = '2026-01-23',
  rate_changed_at = NOW()
WHERE no_unit = 2503;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = '2026-01-23',
  rate_changed_at = NOW()
WHERE no_unit = 2504;
UPDATE inventory_unit SET
  customer_id = 116,
  customer_location_id = @location_202,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2026-01-29',
  rate_changed_at = NOW()
WHERE no_unit = 5575;
UPDATE inventory_unit SET
  customer_id = 154,
  customer_location_id = 394,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 5000000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 5080;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = @contract_520,
  area_id = NULL,
  harga_sewa_bulanan = 13950000.0,
  on_hire_date = '2025-04-22',
  rate_changed_at = NOW()
WHERE no_unit = 5595;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = @contract_520,
  area_id = NULL,
  harga_sewa_bulanan = 13950000.0,
  on_hire_date = '2025-04-22',
  rate_changed_at = NOW()
WHERE no_unit = 5596;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = @contract_531,
  area_id = NULL,
  harga_sewa_bulanan = 13900000.0,
  on_hire_date = '2025-07-25',
  rate_changed_at = NOW()
WHERE no_unit = 5103;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 173,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 29500000.0,
  on_hire_date = '2025-11-04',
  rate_changed_at = NOW()
WHERE no_unit = 2554;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_492,
  area_id = NULL,
  harga_sewa_bulanan = 30500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3579;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = @contract_532,
  area_id = NULL,
  harga_sewa_bulanan = 47500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3580;
UPDATE inventory_unit SET
  customer_id = 189,
  customer_location_id = 434,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5902;

-- ========================================
-- STEP 5: CREATE CUSTOMER_CONTRACTS LINKS
-- ========================================

INSERT IGNORE INTO customer_contracts (customer_id, kontrak_id, is_active)
SELECT DISTINCT iu.customer_id, iu.kontrak_id, 1
FROM inventory_unit iu
WHERE iu.kontrak_id IS NOT NULL
  AND iu.customer_id IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM customer_contracts cc
    WHERE cc.customer_id = iu.customer_id
      AND cc.kontrak_id = iu.kontrak_id
  );

-- ========================================
-- STEP 6: UPDATE CONTRACT TOTAL UNITS
-- ========================================

UPDATE kontrak k
SET total_units = (
  SELECT COUNT(*)
  FROM inventory_unit iu
  WHERE iu.kontrak_id = k.id
);

COMMIT;
SET FOREIGN_KEY_CHECKS=1;

-- ========================================
-- MIGRATION COMPLETE
-- ========================================
