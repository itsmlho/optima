-- OPTIMA ERP - MERGED MARKETING & ACCOUNTING DATA IMPORT
-- Generated: 2026-02-18 22:15:10
-- Total units to import: 2178
-- NOTE: Select database 'u138256737_optima_db' in PHPMyAdmin before running

SET FOREIGN_KEY_CHECKS=0;

-- ========================================
-- STEP 1: RESET OVERLAPPING UNITS
-- Total: 1099 units
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
WHERE no_unit IN (2053, 5293, 2062, 2066, 2088, 5301, 2111, 2131, 2136, 2149, 2150, 2152, 2155, 2161, 2162, 2166, 2167, 2170, 2176, 2180, 3691, 3692, 5623, 2211, 2212, 3696, 2214, 3697, 3698, 5734, 2228, 2232, 3700, 2235, 3701, 2244, 2245, 5738, 2248, 2250, 3704, 2259, 2262, 3707, 2278, 2280, 2282, 3711, 5340, 2293, 2294, 5341, 2297, 3713, 5342, 3715, 2309, 2314, 2315, 2317, 5345, 2319, 3717, 2322, 2323, 5348, 5349, 2342, 2346, 5758, 2351, 5352, 5759, 5760, 5354, 2365, 5763, 5764, 5765, 5766, 5767, 2405, 5363, 5366, 5779, 2461, 5781, 2466, 5782, 2468, 2488, 2499, 2507, 2539, 2542, 2547, 2549, 2550, 2552, 2564);

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
WHERE no_unit IN (2586, 540, 2594, 2595, 5809, 2611, 2612, 2613, 2614, 5589, 5812, 2621, 5813, 5814, 2634, 2638, 2639, 2642, 2645, 2651, 2657, 2671, 2672, 2684, 638, 2686, 2687, 2694, 2696, 2719, 2721, 2722, 2728, 2730, 2733, 2737, 2739, 2740, 2742, 3528, 2754, 2755, 2762, 2766, 3529, 2771, 2777, 2781, 2784, 2785, 2798, 5441, 2801, 2805, 5443, 5445, 2827, 2828, 2834, 2840, 2841, 5449, 2843, 2854, 807, 2855, 2856, 810, 2864, 2869, 822, 2870, 826, 827, 2878, 2881, 3423, 2889, 2890, 2891, 2892, 2897, 2898, 2899, 2900, 2901, 2902, 3425, 2904, 5467, 2931, 5468, 5469, 5470, 5471, 2952, 2953, 5000, 5001, 5472);

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
WHERE no_unit IN (5473, 5010, 5012, 5474, 2967, 2968, 2969, 2970, 2971, 5016, 5017, 5475, 5026, 2985, 2986, 5034, 2988, 2991, 5039, 5041, 2995, 2996, 2997, 2999, 5048, 5481, 3002, 3003, 3004, 3005, 3007, 3009, 3010, 5062, 5064, 5065, 3019, 3020, 5069, 3022, 5071, 3028, 3029, 5079, 3034, 3035, 3036, 5082, 5083, 5085, 3043, 3044, 3045, 3046, 3047, 3048, 3049, 1002, 1003, 3050, 3053, 1006, 3051, 3052, 3056, 3057, 3059, 5107, 3061, 5108, 3063, 3064, 3065, 3066, 3067, 3068, 3069, 3070, 3071, 3072, 3073, 3074, 3075, 3076, 3077, 3078, 3079, 3080, 3081, 3082, 3083, 5126, 5127, 5128, 5134, 5136, 5137, 3090, 5130, 5139);

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
WHERE no_unit IN (1045, 5140, 5142, 5141, 3098, 1051, 1052, 3099, 3100, 5148, 5151, 5153, 3107, 3108, 3109, 5155, 5157, 3112, 3113, 3114, 3115, 3116, 3117, 3118, 3119, 5159, 3121, 3122, 3123, 5164, 5165, 3126, 5167, 5172, 5173, 3133, 3134, 3135, 3136, 3137, 3138, 5185, 3140, 3141, 5186, 5187, 3144, 3145, 5190, 3147, 5192, 5195, 5196, 5197, 5198, 5199, 5200, 5203, 5204, 3157, 5205, 5206, 5208, 5209, 3162, 3163, 5210, 3165, 5212, 5213, 3170, 3171, 3172, 5218, 3174, 5219, 3176, 5109, 5220, 3179, 3180, 3181, 5223, 5224, 5225, 5226, 5227, 5229, 3188, 5230, 5231, 5581, 5112, 3193, 3194, 5242, 5243, 3197, 5113, 5244);

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
WHERE no_unit IN (5245, 5246, 5114, 3203, 5248, 5115, 5257, 5258, 5259, 3212, 3213, 5116, 3215, 3216, 5117, 3218, 5260, 5261, 5262, 5118, 5263, 5268, 5272, 5274, 5119, 5276, 3229, 3230, 5279, 5280, 5120, 3234, 3235, 5281, 3237, 3238, 5121, 5282, 5283, 5284, 3243, 5122, 3245, 5285, 5295, 5296, 5123, 3247, 1202, 5300, 5124, 3252, 5294, 3255, 3256, 5125, 3257, 3258, 3259, 3260, 3261, 3263, 5305, 5306, 5309, 3267, 5310, 3269, 5311, 5312, 5314, 5315, 5316, 5318, 5321, 5322, 5129, 5324, 5325, 5327, 5328, 5330, 5331, 5332, 5333, 5334, 3288, 5335, 5336, 5337, 5338, 5339, 5343, 5344, 3294, 5346, 5347, 3295, 3296, 5350);

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
WHERE no_unit IN (5351, 3297, 3298, 3299, 5355, 3303, 3304, 1257, 1259, 3308, 3311, 3312, 3314, 3316, 5365, 3317, 5362, 3319, 3320, 5370, 3321, 3322, 5364, 3325, 3326, 5367, 5368, 5369, 3331, 5371, 5372, 5373, 5376, 5379, 5380, 5381, 5383, 5384, 5386, 5391, 5393, 5394, 5401, 5408, 3363, 3364, 5416, 3369, 3373, 5422, 3374, 3375, 3376, 3377, 3378, 3379, 5429, 5430, 5431, 3380, 5433, 3381, 3382, 5436, 3383, 5438, 3384, 3385, 3386, 3387, 3388, 3389, 3390, 3391, 3392, 3393, 1346, 3394, 3395, 3396, 3397, 3398, 3400, 3401, 3404, 3405, 5459, 5460, 5461, 3409, 5463, 5464, 3410, 3411, 3412, 1365, 3413, 3414, 3415, 3416);

-- Reset batch 7
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
WHERE no_unit IN (3417, 3418, 3419, 3420, 3422, 5478, 5479, 5480, 5158, 5482, 5483, 3428, 5476, 5477, 1390, 3438, 3440, 5161, 3443, 1396, 3444, 3445, 3446, 3447, 3449, 5162, 3452, 5501, 5502, 3455, 3457, 5509, 3463, 3471, 3472, 3473, 3474, 3475, 1428, 3477, 1430, 1432, 5528, 5529, 3483, 5530, 3485, 3487, 1442, 3491, 5539, 3494, 5579, 5580, 5549, 1453, 3502, 3503, 3504, 3505, 3506, 3507, 3508, 3509, 3510, 1463, 3511, 5562, 5563, 5564, 5565, 5566, 3512, 3513, 3514, 5570, 5571, 3515, 3516, 3517, 3518, 3519, 3520, 3521, 3522, 3523, 3524, 3526, 5583, 3527, 5585, 5586, 3530, 3531, 3532, 3533, 3534, 3535, 3537, 3538);

-- Reset batch 8
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
WHERE no_unit IN (3539, 3541, 3543, 3544, 3545, 3546, 3547, 3548, 1501, 3551, 3552, 5599, 3554, 5602, 5604, 3557, 3558, 5606, 5607, 1513, 3561, 1515, 3562, 3563, 3564, 3565, 3566, 3568, 3570, 3571, 3572, 3573, 3574, 3576, 3577, 3578, 3581, 3582, 3584, 3585, 3586, 3587, 3588, 3589, 3590, 3591, 3592, 3593, 5635, 5636, 5637, 5643, 3598, 3605, 3606, 3607, 3609, 3610, 5657, 3612, 3613, 3615, 5663, 3617, 3618, 3619, 5664, 5665, 3622, 3623, 5666, 3625, 5667, 5668, 3628, 5673, 3630, 5676, 5677, 3633, 3634, 5678, 3636, 5679, 5680, 5682, 5608, 5684, 3645, 3646, 5609, 1600, 1601, 3650, 5610, 3652, 1605, 3653, 3654, 1608);

-- Reset batch 9
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
WHERE no_unit IN (3657, 3658, 5611, 3660, 3661, 5612, 3663, 5703, 3665, 3666, 3667, 5613, 3669, 5614, 5713, 5714, 5718, 5722, 5723, 5724, 5725, 3673, 5719, 3675, 5729, 3676, 3677, 3678, 3679, 3680, 1633, 1634, 3681, 3682, 5739, 3683, 3684, 3686, 5619, 3687, 3688, 5746, 5747, 5748, 5749, 5620, 3694, 5752, 5753, 5754, 5755, 3699, 1652, 5622, 3702, 3703, 5761, 3706, 1659, 1660, 3708, 3709, 1664, 5768, 5769, 5770, 5771, 5772, 5773, 5774, 5775, 5776, 5624, 1674, 5625, 3729, 3730, 3731, 3732, 3733, 3734, 5626, 5627, 3737, 5780, 5628, 3741, 5786, 3745, 5629, 5791, 5792, 5794, 5630, 5797, 5798, 5800, 3754, 5631, 5801);

-- Reset batch 10
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
WHERE no_unit IN (5803, 3758, 5807, 5808, 5804, 5810, 5811, 5805, 3761, 5806, 3763, 3764, 3765, 3766, 3767, 3768, 3769, 3771, 3772, 5820, 3777, 5822, 5823, 3783, 1736, 3784, 3785, 3786, 3787, 3788, 3790, 5582, 3792, 3793, 3794, 3795, 3796, 6045, 3798, 3801, 3802, 3803, 3804, 1760, 3811, 3812, 3815, 3817, 3818, 3819, 3820, 1773, 3822, 3823, 5865, 1777, 3825, 5866, 3828, 3830, 1783, 3832, 3833, 3834, 3835, 3836, 5882, 3838, 3841, 3843, 5893, 3846, 5895, 5894, 3848, 3849, 3850, 3851, 3852, 3853, 3854, 3855, 3856, 5907, 3860, 3861, 3862, 3863, 1816, 3864, 3867, 5916, 3869, 3870, 3874, 1828, 3878, 3879, 3884, 5933);

-- Reset batch 11
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
WHERE no_unit IN (3888, 3892, 3893, 3902, 3906, 3912, 3913, 3914, 3915, 3916, 3917, 3918, 3919, 3920, 3921, 3922, 3924, 3927, 3930, 3931, 1885, 3934, 5985, 3938, 3939, 5990, 5992, 5994, 5995, 6000, 6001, 6002, 3955, 3956, 6003, 6004, 6006, 3960, 6007, 6009, 6011, 6013, 6014, 6015, 6018, 3971, 6020, 3974, 6023, 6024, 6025, 1930, 1931, 6026, 3981, 3982, 6029, 6032, 6033, 6031, 3985, 1938, 3986, 3988, 3989, 3990, 3991, 1944, 3992, 1946, 3993, 3994, 3995, 3996, 3997, 1952, 3998, 3999, 6044, 1956, 1974, 6070, 1976, 1977, 1978, 1979, 6072, 5584, 1993, 5615, 2001, 2013, 2018, 5286, 5616, 5288, 5289, 5290, 5617);

-- ========================================
-- STEP 2: INSERT NEW CUSTOMER LOCATIONS
-- Total: 35 locations
-- ========================================

-- Location 1: Hyundai
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (86, NULL, 'Hyundai', 'LOC-86-1', 'Auto-generated from merge', 'Hyundai', 'HYUNDAI', 0, 1);
SET @location_1 = LAST_INSERT_ID();

-- Location 2: Semarang
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (92, 35, 'Semarang', 'LOC-92-2', 'Auto-generated from merge', 'Semarang', 'SEMARANG', 0, 1);
SET @location_2 = LAST_INSERT_ID();

-- Location 3: Majalengka
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (55, 26, 'Majalengka', 'LOC-55-3', 'Auto-generated from merge', 'Majalengka', 'MAJALENGKA', 0, 1);
SET @location_3 = LAST_INSERT_ID();

-- Location 4: Bandung
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (1, NULL, 'Bandung', 'LOC-1-4', 'Auto-generated from merge', 'Bandung', 'BANDUNG', 0, 1);
SET @location_4 = LAST_INSERT_ID();

-- Location 5: Karawang
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (92, 19, 'Karawang', 'LOC-92-5', 'Auto-generated from merge', 'Karawang', 'KARAWANG', 0, 1);
SET @location_5 = LAST_INSERT_ID();

-- Location 6: Karawang
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (56, 19, 'Karawang', 'LOC-56-6', 'Auto-generated from merge', 'Karawang', 'KARAWANG', 0, 1);
SET @location_6 = LAST_INSERT_ID();

-- Location 7: Karawang
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (6, 19, 'Karawang', 'LOC-6-7', 'Auto-generated from merge', 'Karawang', 'KARAWANG', 0, 1);
SET @location_7 = LAST_INSERT_ID();

-- Location 8: Bogor
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (13, NULL, 'Bogor', 'LOC-13-8', 'Auto-generated from merge', 'Bogor', 'BOGOR', 0, 1);
SET @location_8 = LAST_INSERT_ID();

-- Location 9: Rancaekek
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (55, NULL, 'Rancaekek', 'LOC-55-9', 'Auto-generated from merge', 'Rancaekek', 'RANCAEKEK', 0, 1);
SET @location_9 = LAST_INSERT_ID();

-- Location 10: Cikampek
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (80, NULL, 'Cikampek', 'LOC-80-10', 'Auto-generated from merge', 'Cikampek', 'CIKAMPEK', 0, 1);
SET @location_10 = LAST_INSERT_ID();

-- Location 11: Bogor
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (7, NULL, 'Bogor', 'LOC-7-11', 'Auto-generated from merge', 'Bogor', 'BOGOR', 0, 1);
SET @location_11 = LAST_INSERT_ID();

-- Location 12: Purwosari
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (51, NULL, 'Purwosari', 'LOC-51-12', 'Auto-generated from merge', 'Purwosari', 'PURWOSARI', 0, 1);
SET @location_12 = LAST_INSERT_ID();

-- Location 13: Subang
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (90, NULL, 'Subang', 'LOC-90-13', 'Auto-generated from merge', 'Subang', 'SUBANG', 0, 1);
SET @location_13 = LAST_INSERT_ID();

-- Location 14: Cirebon
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (65, NULL, 'Cirebon', 'LOC-65-14', 'Auto-generated from merge', 'Cirebon', 'CIREBON', 0, 1);
SET @location_14 = LAST_INSERT_ID();

-- Location 15: Ciracas
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (51, NULL, 'Ciracas', 'LOC-51-15', 'Auto-generated from merge', 'Ciracas', 'CIRACAS', 0, 1);
SET @location_15 = LAST_INSERT_ID();

-- Location 16: Pandaan
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (51, NULL, 'Pandaan', 'LOC-51-16', 'Auto-generated from merge', 'Pandaan', 'PANDAAN', 0, 1);
SET @location_16 = LAST_INSERT_ID();

-- Location 17: Ejip
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (100, NULL, 'Ejip', 'LOC-100-17', 'Auto-generated from merge', 'Ejip', 'EJIP', 0, 1);
SET @location_17 = LAST_INSERT_ID();

-- Location 18: Cakung
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (49, NULL, 'Cakung', 'LOC-49-18', 'Auto-generated from merge', 'Cakung', 'JAKARTA', 0, 1);
SET @location_18 = LAST_INSERT_ID();

-- Location 19: Karawang
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (16, 19, 'Karawang', 'LOC-16-19', 'Auto-generated from merge', 'Karawang', 'KARAWANG', 0, 1);
SET @location_19 = LAST_INSERT_ID();

-- Location 20: LDC Pasar Rebo
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (4, NULL, 'LDC Pasar Rebo', 'LOC-4-20', 'Auto-generated from merge', 'LDC Pasar Rebo', 'JAKARTA', 0, 1);
SET @location_20 = LAST_INSERT_ID();

-- Location 21: Surabaya
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (92, 33, 'Surabaya', 'LOC-92-21', 'Auto-generated from merge', 'Surabaya', 'SURABAYA', 0, 1);
SET @location_21 = LAST_INSERT_ID();

-- Location 22: JABABEKA
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (53, NULL, 'JABABEKA', 'LOC-53-22', 'Auto-generated from merge', 'JABABEKA', 'JABABEKA - 1', 0, 1);
SET @location_22 = LAST_INSERT_ID();

-- Location 23: Malang
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (4, NULL, 'Malang', 'LOC-4-23', 'Auto-generated from merge', 'Malang', 'MALANG', 0, 1);
SET @location_23 = LAST_INSERT_ID();

-- Location 24: Cikupa
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (4, 13, 'Cikupa', 'LOC-4-24', 'Auto-generated from merge', 'Cikupa', 'CIKUPA', 0, 1);
SET @location_24 = LAST_INSERT_ID();

-- Location 25: Sawangan
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (4, 16, 'Sawangan', 'LOC-4-25', 'Auto-generated from merge', 'Sawangan', 'SAWANGAN', 0, 1);
SET @location_25 = LAST_INSERT_ID();

-- Location 26: Jakarta
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (42, NULL, 'Jakarta', 'LOC-42-26', 'Auto-generated from merge', 'Jakarta', 'PULO GADUNG', 0, 1);
SET @location_26 = LAST_INSERT_ID();

-- Location 27: SIDOARJO
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (81, NULL, 'SIDOARJO', 'LOC-81-27', 'Auto-generated from merge', 'SIDOARJO', 'SIDOARJO', 0, 1);
SET @location_27 = LAST_INSERT_ID();

-- Location 28: JOMBANG
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (199, NULL, 'JOMBANG', 'LOC-199-28', 'Auto-generated from merge', 'JOMBANG', 'JOMBANG', 0, 1);
SET @location_28 = LAST_INSERT_ID();

-- Location 29: Bogor
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (47, NULL, 'Bogor', 'LOC-47-29', 'Auto-generated from merge', 'Bogor', 'BOGOR', 0, 1);
SET @location_29 = LAST_INSERT_ID();

-- Location 30: Cicurug
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (4, NULL, 'Cicurug', 'LOC-4-30', 'Auto-generated from merge', 'Cicurug', 'SUKABUMI', 0, 1);
SET @location_30 = LAST_INSERT_ID();

-- Location 31: Jababeka
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (103, NULL, 'Jababeka', 'LOC-103-31', 'Auto-generated from merge', 'Jababeka', 'JABABEKA', 0, 1);
SET @location_31 = LAST_INSERT_ID();

-- Location 32: Cibitung
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (19, NULL, 'Cibitung', 'LOC-19-32', 'Auto-generated from accounting data', 'Cibitung', 'N/A', 0, 1);
SET @location_32 = LAST_INSERT_ID();

-- Location 33: PALEMBANG
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (3, NULL, 'PALEMBANG', 'LOC-3-33', 'Auto-generated from accounting data', 'PALEMBANG', 'N/A', 0, 1);
SET @location_33 = LAST_INSERT_ID();

-- Location 34: MUARA ANGKE
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (61, NULL, 'MUARA ANGKE', 'LOC-61-34', 'Auto-generated from accounting data', 'MUARA ANGKE', 'N/A', 0, 1);
SET @location_34 = LAST_INSERT_ID();

-- Location 35: Lampung
INSERT INTO customer_locations (customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)
VALUES (63, NULL, 'Lampung', 'LOC-63-35', 'Auto-generated from accounting data', 'Lampung', 'N/A', 0, 1);
SET @location_35 = LAST_INSERT_ID();

-- ========================================
-- STEP 3: INSERT NEW CONTRACTS
-- Total: 6 contracts
-- ========================================

-- Contract 1: PO Perbulan
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, no_po_marketing, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (228, 'PO Perbulan', 'PO_ONLY', 'PO Perbulan', '2026-01-01', '2026-12-31', 'Aktif', 0);
SET @contract_1 = LAST_INSERT_ID();

-- Contract 2: po/bulan
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, no_po_marketing, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (950, 'po/bulan', 'PO_ONLY', 'po/bulan', '2023-01-22', '2026-05-21', 'Aktif', 0);
SET @contract_2 = LAST_INSERT_ID();

-- Contract 3: Belum terima PO
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, no_po_marketing, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (@location_3, 'Belum terima PO', 'PO_ONLY', 'Belum terima PO', '2026-02-18', '2027-02-18', 'Pending', 0);
SET @contract_3 = LAST_INSERT_ID();

-- Contract 4: PO PERBULAN
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, no_po_marketing, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (979, 'PO PERBULAN', 'PO_ONLY', 'PO PERBULAN', '2026-02-18', '2027-02-18', 'Pending', 0);
SET @contract_4 = LAST_INSERT_ID();

-- Contract 5: PO/Bulan
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, no_po_marketing, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (1041, 'PO/Bulan', 'PO_ONLY', 'PO/Bulan', '2026-02-18', '2027-02-18', 'Pending', 0);
SET @contract_5 = LAST_INSERT_ID();

-- Contract 6: Spare
INSERT INTO kontrak (customer_location_id, no_kontrak, rental_type, no_po_marketing, tanggal_mulai, tanggal_berakhir, status, total_units)
VALUES (1087, 'Spare', 'PO_ONLY', 'Spare', '2026-02-18', '2027-02-18', 'Pending', 0);
SET @contract_6 = LAST_INSERT_ID();

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
  kontrak_id = 368,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2021-08-21',
  rate_changed_at = NOW()
WHERE no_unit = 2062;
UPDATE inventory_unit SET
  customer_id = 125,
  customer_location_id = 336,
  kontrak_id = 369,
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
  kontrak_id = 369,
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
  kontrak_id = 370,
  area_id = 17,
  harga_sewa_bulanan = 7700.0,
  on_hire_date = '2025-07-29',
  rate_changed_at = NOW()
WHERE no_unit = 5300;
UPDATE inventory_unit SET
  customer_id = 228,
  customer_location_id = 908,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2111;
UPDATE inventory_unit SET
  customer_id = 24,
  customer_location_id = 909,
  kontrak_id = 371,
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
  kontrak_id = 372,
  area_id = NULL,
  harga_sewa_bulanan = 9500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2136;
UPDATE inventory_unit SET
  customer_id = 94,
  customer_location_id = 275,
  kontrak_id = 373,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2022-12-14',
  rate_changed_at = NOW()
WHERE no_unit = 2149;
UPDATE inventory_unit SET
  customer_id = 71,
  customer_location_id = 216,
  kontrak_id = 374,
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
  kontrak_id = 374,
  area_id = NULL,
  harga_sewa_bulanan = 5850000.0,
  on_hire_date = '2022-02-23',
  rate_changed_at = NOW()
WHERE no_unit = 2155;
UPDATE inventory_unit SET
  customer_id = 86,
  customer_location_id = @location_1,
  kontrak_id = 375,
  area_id = NULL,
  harga_sewa_bulanan = 5500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2161;
UPDATE inventory_unit SET
  customer_id = 177,
  customer_location_id = 422,
  kontrak_id = 376,
  area_id = 21,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2162;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 911,
  kontrak_id = 377,
  area_id = NULL,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = '2025-07-14',
  rate_changed_at = NOW()
WHERE no_unit = 5722;
UPDATE inventory_unit SET
  customer_id = 49,
  customer_location_id = 163,
  kontrak_id = 378,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2025-08-05',
  rate_changed_at = NOW()
WHERE no_unit = 2166;
UPDATE inventory_unit SET
  customer_id = 71,
  customer_location_id = 216,
  kontrak_id = 374,
  area_id = NULL,
  harga_sewa_bulanan = 5850000.0,
  on_hire_date = '2022-02-23',
  rate_changed_at = NOW()
WHERE no_unit = 2167;
UPDATE inventory_unit SET
  customer_id = 86,
  customer_location_id = @location_1,
  kontrak_id = 375,
  area_id = NULL,
  harga_sewa_bulanan = 5500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2170;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 141,
  kontrak_id = 377,
  area_id = NULL,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = '2025-07-14',
  rate_changed_at = NOW()
WHERE no_unit = 5723;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 911,
  kontrak_id = 377,
  area_id = NULL,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = '2025-07-14',
  rate_changed_at = NOW()
WHERE no_unit = 5724;
UPDATE inventory_unit SET
  customer_id = 177,
  customer_location_id = 422,
  kontrak_id = 376,
  area_id = 21,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2176;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 911,
  kontrak_id = 379,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2024-12-30',
  rate_changed_at = NOW()
WHERE no_unit = 2180;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_2,
  kontrak_id = 380,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5725;
UPDATE inventory_unit SET
  customer_id = 162,
  customer_location_id = 404,
  kontrak_id = 381,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5729;
UPDATE inventory_unit SET
  customer_id = 166,
  customer_location_id = 408,
  kontrak_id = 382,
  area_id = NULL,
  harga_sewa_bulanan = 6750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2211;
UPDATE inventory_unit SET
  customer_id = 166,
  customer_location_id = 408,
  kontrak_id = 382,
  area_id = NULL,
  harga_sewa_bulanan = 6750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2212;
UPDATE inventory_unit SET
  customer_id = 166,
  customer_location_id = 408,
  kontrak_id = 382,
  area_id = NULL,
  harga_sewa_bulanan = 7200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2214;
UPDATE inventory_unit SET
  customer_id = 166,
  customer_location_id = 408,
  kontrak_id = 382,
  area_id = NULL,
  harga_sewa_bulanan = 6750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2228;
UPDATE inventory_unit SET
  customer_id = 64,
  customer_location_id = 913,
  kontrak_id = 383,
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
  customer_location_id = 909,
  kontrak_id = 384,
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
  kontrak_id = 382,
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
  customer_location_id = 909,
  kontrak_id = 385,
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
  kontrak_id = 386,
  area_id = NULL,
  harga_sewa_bulanan = 9900000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2262;
UPDATE inventory_unit SET
  customer_id = 30,
  customer_location_id = 99,
  kontrak_id = 387,
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
  customer_location_id = 914,
  kontrak_id = 388,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = '2025-07-29',
  rate_changed_at = NOW()
WHERE no_unit = 2280;
UPDATE inventory_unit SET
  customer_id = 24,
  customer_location_id = 909,
  kontrak_id = 389,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2024-10-07',
  rate_changed_at = NOW()
WHERE no_unit = 2282;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 390,
  area_id = 26,
  harga_sewa_bulanan = 22500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5746;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 132,
  kontrak_id = 379,
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
  customer_location_id = @location_4,
  kontrak_id = 391,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2022-06-09',
  rate_changed_at = NOW()
WHERE no_unit = 2294;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 911,
  kontrak_id = 379,
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
  customer_location_id = 911,
  kontrak_id = 379,
  area_id = NULL,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2023-05-31',
  rate_changed_at = NOW()
WHERE no_unit = 5749;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 8400000.0,
  on_hire_date = '2025-02-26',
  rate_changed_at = NOW()
WHERE no_unit = 5343;
UPDATE inventory_unit SET
  customer_id = 145,
  customer_location_id = 383,
  kontrak_id = 393,
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
  kontrak_id = 394,
  area_id = NULL,
  harga_sewa_bulanan = 30000000.0,
  on_hire_date = '2025-03-27',
  rate_changed_at = NOW()
WHERE no_unit = 2314;
UPDATE inventory_unit SET
  customer_id = 145,
  customer_location_id = 383,
  kontrak_id = 394,
  area_id = NULL,
  harga_sewa_bulanan = 30000000.0,
  on_hire_date = '2025-06-24',
  rate_changed_at = NOW()
WHERE no_unit = 2315;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 112,
  kontrak_id = 395,
  area_id = NULL,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2025-08-21',
  rate_changed_at = NOW()
WHERE no_unit = 5752;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 8400000.0,
  on_hire_date = '2025-02-25',
  rate_changed_at = NOW()
WHERE no_unit = 2317;
UPDATE inventory_unit SET
  customer_id = 137,
  customer_location_id = 356,
  kontrak_id = 396,
  area_id = 33,
  harga_sewa_bulanan = 11800000.0,
  on_hire_date = '2023-10-07',
  rate_changed_at = NOW()
WHERE no_unit = 2319;
UPDATE inventory_unit SET
  customer_id = 30,
  customer_location_id = 99,
  kontrak_id = 387,
  area_id = 33,
  harga_sewa_bulanan = 16200000.0,
  on_hire_date = '2025-12-06',
  rate_changed_at = NOW()
WHERE no_unit = 5753;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_5,
  kontrak_id = 397,
  area_id = 19,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2322;
UPDATE inventory_unit SET
  customer_id = 162,
  customer_location_id = 404,
  kontrak_id = 398,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2323;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 8400000.0,
  on_hire_date = '2025-02-26',
  rate_changed_at = NOW()
WHERE no_unit = 5346;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_6,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2025-08-28',
  rate_changed_at = NOW()
WHERE no_unit = 5754;
UPDATE inventory_unit SET
  customer_id = 27,
  customer_location_id = 86,
  kontrak_id = 399,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2024-12-18',
  rate_changed_at = NOW()
WHERE no_unit = 5347;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_6,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2025-08-28',
  rate_changed_at = NOW()
WHERE no_unit = 5755;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 244,
  kontrak_id = 400,
  area_id = NULL,
  harga_sewa_bulanan = 13900000.0,
  on_hire_date = '2023-11-24',
  rate_changed_at = NOW()
WHERE no_unit = 2342;
UPDATE inventory_unit SET
  customer_id = 9,
  customer_location_id = 62,
  kontrak_id = 401,
  area_id = 2,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = '2024-09-03',
  rate_changed_at = NOW()
WHERE no_unit = 5350;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 421,
  kontrak_id = 402,
  area_id = NULL,
  harga_sewa_bulanan = 15500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2346;
UPDATE inventory_unit SET
  customer_id = 113,
  customer_location_id = 317,
  kontrak_id = 368,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-10-05',
  rate_changed_at = NOW()
WHERE no_unit = 5351;
UPDATE inventory_unit SET
  customer_id = 145,
  customer_location_id = 383,
  kontrak_id = 393,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2023-08-02',
  rate_changed_at = NOW()
WHERE no_unit = 2351;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_6,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-08-29',
  rate_changed_at = NOW()
WHERE no_unit = 5761;
UPDATE inventory_unit SET
  customer_id = 11,
  customer_location_id = 919,
  kontrak_id = 403,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2365;
UPDATE inventory_unit SET
  customer_id = 133,
  customer_location_id = 352,
  kontrak_id = 404,
  area_id = 12,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = '2026-01-02',
  rate_changed_at = NOW()
WHERE no_unit = 5355;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 113,
  kontrak_id = 405,
  area_id = NULL,
  harga_sewa_bulanan = 10050000.0,
  on_hire_date = '2025-08-26',
  rate_changed_at = NOW()
WHERE no_unit = 5768;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 113,
  kontrak_id = 406,
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
  kontrak_id = 407,
  area_id = NULL,
  harga_sewa_bulanan = 10050000.0,
  on_hire_date = '2025-08-26',
  rate_changed_at = NOW()
WHERE no_unit = 5770;
UPDATE inventory_unit SET
  customer_id = 30,
  customer_location_id = 98,
  kontrak_id = 408,
  area_id = NULL,
  harga_sewa_bulanan = 9200000.0,
  on_hire_date = '2025-12-05',
  rate_changed_at = NOW()
WHERE no_unit = 5771;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = 400,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-09-26',
  rate_changed_at = NOW()
WHERE no_unit = 5365;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 114,
  kontrak_id = 409,
  area_id = NULL,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2025-08-21',
  rate_changed_at = NOW()
WHERE no_unit = 5772;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 113,
  kontrak_id = 410,
  area_id = NULL,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2025-08-21',
  rate_changed_at = NOW()
WHERE no_unit = 5773;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 112,
  kontrak_id = 411,
  area_id = NULL,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2025-08-26',
  rate_changed_at = NOW()
WHERE no_unit = 5774;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 114,
  kontrak_id = 412,
  area_id = NULL,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2025-08-26',
  rate_changed_at = NOW()
WHERE no_unit = 5775;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 112,
  kontrak_id = 413,
  area_id = NULL,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2025-08-26',
  rate_changed_at = NOW()
WHERE no_unit = 5776;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 249,
  kontrak_id = 400,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-09-26',
  rate_changed_at = NOW()
WHERE no_unit = 5370;
UPDATE inventory_unit SET
  customer_id = 155,
  customer_location_id = 396,
  kontrak_id = 414,
  area_id = 19,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2025-10-27',
  rate_changed_at = NOW()
WHERE no_unit = 2461;
UPDATE inventory_unit SET
  customer_id = 230,
  customer_location_id = 920,
  kontrak_id = 415,
  area_id = 19,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = '2026-01-08',
  rate_changed_at = NOW()
WHERE no_unit = 2466;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_7,
  kontrak_id = 416,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2021-07-28',
  rate_changed_at = NOW()
WHERE no_unit = 2468;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 417,
  area_id = 35,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = '2025-12-10',
  rate_changed_at = NOW()
WHERE no_unit = 2488;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 417,
  area_id = 35,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = '2025-12-10',
  rate_changed_at = NOW()
WHERE no_unit = 2499;
UPDATE inventory_unit SET
  customer_id = 122,
  customer_location_id = 329,
  kontrak_id = 418,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = '2025-12-06',
  rate_changed_at = NOW()
WHERE no_unit = 2507;
UPDATE inventory_unit SET
  customer_id = 9,
  customer_location_id = 62,
  kontrak_id = 419,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = '2023-05-13',
  rate_changed_at = NOW()
WHERE no_unit = 2539;
UPDATE inventory_unit SET
  customer_id = 13,
  customer_location_id = @location_8,
  kontrak_id = 420,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2542;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 53,
  kontrak_id = 386,
  area_id = NULL,
  harga_sewa_bulanan = 7150000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2547;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 49,
  kontrak_id = 386,
  area_id = NULL,
  harga_sewa_bulanan = 7150000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2549;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 48,
  kontrak_id = 386,
  area_id = NULL,
  harga_sewa_bulanan = 7150000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2550;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 2552;

-- Processed 100/2178 units

UPDATE inventory_unit SET
  customer_id = 80,
  customer_location_id = @location_10,
  kontrak_id = 421,
  area_id = NULL,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = '2023-02-15',
  rate_changed_at = NOW()
WHERE no_unit = 2564;
UPDATE inventory_unit SET
  customer_id = 166,
  customer_location_id = 408,
  kontrak_id = 382,
  area_id = NULL,
  harga_sewa_bulanan = 6750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2586;
UPDATE inventory_unit SET
  customer_id = 153,
  customer_location_id = 925,
  kontrak_id = 422,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = '2025-08-22',
  rate_changed_at = NOW()
WHERE no_unit = 540;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = 423,
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
  kontrak_id = 424,
  area_id = NULL,
  harga_sewa_bulanan = 16200000.0,
  on_hire_date = '2025-09-03',
  rate_changed_at = NOW()
WHERE no_unit = 5808;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = 423,
  area_id = NULL,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5810;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 47,
  kontrak_id = 386,
  area_id = NULL,
  harga_sewa_bulanan = 7150000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2611;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 47,
  kontrak_id = 386,
  area_id = NULL,
  harga_sewa_bulanan = 7150000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2612;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 50,
  kontrak_id = 386,
  area_id = NULL,
  harga_sewa_bulanan = 7150000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2613;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 51,
  kontrak_id = 386,
  area_id = NULL,
  harga_sewa_bulanan = 7150000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2614;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = 423,
  area_id = NULL,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5811;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = 926,
  kontrak_id = 425,
  area_id = 20,
  harga_sewa_bulanan = 48500000.0,
  on_hire_date = '2023-06-27',
  rate_changed_at = NOW()
WHERE no_unit = 2621;
UPDATE inventory_unit SET
  customer_id = 122,
  customer_location_id = 329,
  kontrak_id = 426,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2025-12-18',
  rate_changed_at = NOW()
WHERE no_unit = 2634;
UPDATE inventory_unit SET
  customer_id = 11,
  customer_location_id = 919,
  kontrak_id = 427,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2638;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = 428,
  area_id = 18,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-05-16',
  rate_changed_at = NOW()
WHERE no_unit = 2639;
UPDATE inventory_unit SET
  customer_id = 29,
  customer_location_id = 92,
  kontrak_id = 429,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2642;
UPDATE inventory_unit SET
  customer_id = 99,
  customer_location_id = 927,
  kontrak_id = 430,
  area_id = NULL,
  harga_sewa_bulanan = 7800000.0,
  on_hire_date = '2025-11-21',
  rate_changed_at = NOW()
WHERE no_unit = 2645;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 53,
  kontrak_id = 386,
  area_id = NULL,
  harga_sewa_bulanan = 8750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2651;
UPDATE inventory_unit SET
  customer_id = 72,
  customer_location_id = 218,
  kontrak_id = 431,
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
  customer_location_id = 928,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2672;
UPDATE inventory_unit SET
  customer_id = 229,
  customer_location_id = 929,
  kontrak_id = 402,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2684;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 928,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 9300000.0,
  on_hire_date = '2022-01-07',
  rate_changed_at = NOW()
WHERE no_unit = 638;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 930,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2024-07-20',
  rate_changed_at = NOW()
WHERE no_unit = 2686;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 931,
  kontrak_id = 432,
  area_id = 17,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = '2024-10-04',
  rate_changed_at = NOW()
WHERE no_unit = 2687;
UPDATE inventory_unit SET
  customer_id = 178,
  customer_location_id = 423,
  kontrak_id = 433,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2694;
UPDATE inventory_unit SET
  customer_id = 25,
  customer_location_id = 84,
  kontrak_id = 434,
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
  kontrak_id = 435,
  area_id = NULL,
  harga_sewa_bulanan = 33150000.0,
  on_hire_date = '2025-11-06',
  rate_changed_at = NOW()
WHERE no_unit = 2721;
UPDATE inventory_unit SET
  customer_id = 24,
  customer_location_id = 914,
  kontrak_id = 436,
  area_id = NULL,
  harga_sewa_bulanan = 30500000.0,
  on_hire_date = '2025-07-26',
  rate_changed_at = NOW()
WHERE no_unit = 2722;
UPDATE inventory_unit SET
  customer_id = 184,
  customer_location_id = 429,
  kontrak_id = 437,
  area_id = NULL,
  harga_sewa_bulanan = 10800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2728;
UPDATE inventory_unit SET
  customer_id = 194,
  customer_location_id = 439,
  kontrak_id = 438,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2730;
UPDATE inventory_unit SET
  customer_id = 188,
  customer_location_id = 433,
  kontrak_id = 439,
  area_id = 13,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2733;
UPDATE inventory_unit SET
  customer_id = 79,
  customer_location_id = 228,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2025-03-11',
  rate_changed_at = NOW()
WHERE no_unit = 5563;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_5,
  kontrak_id = 441,
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
  customer_location_id = @location_11,
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
  customer_location_id = @location_11,
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
  customer_location_id = 911,
  kontrak_id = 379,
  area_id = NULL,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-08-07',
  rate_changed_at = NOW()
WHERE no_unit = 2754;
UPDATE inventory_unit SET
  customer_id = 21,
  customer_location_id = 933,
  kontrak_id = 442,
  area_id = NULL,
  harga_sewa_bulanan = 21666667.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2755;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 123,
  kontrak_id = 370,
  area_id = 17,
  harga_sewa_bulanan = 7700.0,
  on_hire_date = '2025-07-29',
  rate_changed_at = NOW()
WHERE no_unit = 5433;
UPDATE inventory_unit SET
  customer_id = 79,
  customer_location_id = 228,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2025-03-11',
  rate_changed_at = NOW()
WHERE no_unit = 5564;
UPDATE inventory_unit SET
  customer_id = 118,
  customer_location_id = 322,
  kontrak_id = 368,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2022-01-25',
  rate_changed_at = NOW()
WHERE no_unit = 2762;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 47,
  kontrak_id = 386,
  area_id = NULL,
  harga_sewa_bulanan = 7150000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2766;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_7,
  kontrak_id = 416,
  area_id = 19,
  harga_sewa_bulanan = 18000000.0,
  on_hire_date = '2021-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 2771;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 123,
  kontrak_id = 370,
  area_id = 17,
  harga_sewa_bulanan = 7700.0,
  on_hire_date = '2025-07-29',
  rate_changed_at = NOW()
WHERE no_unit = 5436;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_7,
  kontrak_id = 416,
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
  kontrak_id = 443,
  area_id = 19,
  harga_sewa_bulanan = 9100000.0,
  on_hire_date = '2021-06-28',
  rate_changed_at = NOW()
WHERE no_unit = 2784;
UPDATE inventory_unit SET
  customer_id = 7,
  customer_location_id = @location_11,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 50000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2785;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_6,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2025-09-05',
  rate_changed_at = NOW()
WHERE no_unit = 5438;
UPDATE inventory_unit SET
  customer_id = 79,
  customer_location_id = 228,
  kontrak_id = @contract_1,
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
  kontrak_id = 444,
  area_id = NULL,
  harga_sewa_bulanan = 3250000.0,
  on_hire_date = '2021-08-12',
  rate_changed_at = NOW()
WHERE no_unit = 2805;
UPDATE inventory_unit SET
  customer_id = 79,
  customer_location_id = 228,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2025-03-11',
  rate_changed_at = NOW()
WHERE no_unit = 5566;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 48,
  kontrak_id = 386,
  area_id = NULL,
  harga_sewa_bulanan = 10400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2827;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 175,
  kontrak_id = 445,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2828;
UPDATE inventory_unit SET
  customer_id = 9,
  customer_location_id = 62,
  kontrak_id = 446,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = '2024-01-09',
  rate_changed_at = NOW()
WHERE no_unit = 2834;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 419,
  kontrak_id = 402,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2840;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 419,
  kontrak_id = 402,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2841;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 419,
  kontrak_id = 402,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2843;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_7,
  kontrak_id = 416,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2021-08-25',
  rate_changed_at = NOW()
WHERE no_unit = 2854;
UPDATE inventory_unit SET
  customer_id = 35,
  customer_location_id = 934,
  kontrak_id = 382,
  area_id = NULL,
  harga_sewa_bulanan = 9100000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 807;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_7,
  kontrak_id = 416,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2021-08-25',
  rate_changed_at = NOW()
WHERE no_unit = 2855;
UPDATE inventory_unit SET
  customer_id = 14,
  customer_location_id = 68,
  kontrak_id = 447,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2856;
UPDATE inventory_unit SET
  customer_id = 129,
  customer_location_id = 341,
  kontrak_id = 368,
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
  customer_location_id = @location_3,
  kontrak_id = 448,
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
  kontrak_id = 437,
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
  kontrak_id = 449,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2023-01-16',
  rate_changed_at = NOW()
WHERE no_unit = 2878;
UPDATE inventory_unit SET
  customer_id = 177,
  customer_location_id = 422,
  kontrak_id = 376,
  area_id = NULL,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2881;
UPDATE inventory_unit SET
  customer_id = 73,
  customer_location_id = 220,
  kontrak_id = 450,
  area_id = NULL,
  harga_sewa_bulanan = 10700000.0,
  on_hire_date = '2021-10-02',
  rate_changed_at = NOW()
WHERE no_unit = 2889;
UPDATE inventory_unit SET
  customer_id = 73,
  customer_location_id = 220,
  kontrak_id = 450,
  area_id = NULL,
  harga_sewa_bulanan = 9700000.0,
  on_hire_date = '2021-04-21',
  rate_changed_at = NOW()
WHERE no_unit = 2890;
UPDATE inventory_unit SET
  customer_id = 73,
  customer_location_id = 220,
  kontrak_id = 450,
  area_id = NULL,
  harga_sewa_bulanan = 9700000.0,
  on_hire_date = '2021-02-22',
  rate_changed_at = NOW()
WHERE no_unit = 2891;
UPDATE inventory_unit SET
  customer_id = 73,
  customer_location_id = 220,
  kontrak_id = 450,
  area_id = NULL,
  harga_sewa_bulanan = 14700000.0,
  on_hire_date = '2021-04-19',
  rate_changed_at = NOW()
WHERE no_unit = 2892;
UPDATE inventory_unit SET
  customer_id = 24,
  customer_location_id = 909,
  kontrak_id = 385,
  area_id = 33,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = '2025-08-11',
  rate_changed_at = NOW()
WHERE no_unit = 5459;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 390,
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
  customer_location_id = 911,
  kontrak_id = 451,
  area_id = NULL,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2023-06-23',
  rate_changed_at = NOW()
WHERE no_unit = 2902;
UPDATE inventory_unit SET
  customer_id = 30,
  customer_location_id = 94,
  kontrak_id = 452,
  area_id = 19,
  harga_sewa_bulanan = 10700000.0,
  on_hire_date = '2024-12-03',
  rate_changed_at = NOW()
WHERE no_unit = 5461;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 240,
  kontrak_id = 453,
  area_id = NULL,
  harga_sewa_bulanan = 19000000.0,
  on_hire_date = '2023-01-06',
  rate_changed_at = NOW()
WHERE no_unit = 2904;
UPDATE inventory_unit SET
  customer_id = 64,
  customer_location_id = 913,
  kontrak_id = 454,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-12-13',
  rate_changed_at = NOW()
WHERE no_unit = 5463;
UPDATE inventory_unit SET
  customer_id = 64,
  customer_location_id = 913,
  kontrak_id = 454,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-12-13',
  rate_changed_at = NOW()
WHERE no_unit = 5464;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 935,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 10800000.0,
  on_hire_date = '2021-12-21',
  rate_changed_at = NOW()
WHERE no_unit = 2931;
UPDATE inventory_unit SET
  customer_id = 216,
  customer_location_id = 936,
  kontrak_id = 455,
  area_id = 19,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2952;
UPDATE inventory_unit SET
  customer_id = 80,
  customer_location_id = @location_10,
  kontrak_id = 456,
  area_id = NULL,
  harga_sewa_bulanan = 28000000.0,
  on_hire_date = '2025-11-13',
  rate_changed_at = NOW()
WHERE no_unit = 2953;

-- Processed 200/2178 units

UPDATE inventory_unit SET
  customer_id = 170,
  customer_location_id = 937,
  kontrak_id = 457,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5000;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 5000000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 5001;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 458,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5010;
UPDATE inventory_unit SET
  customer_id = 44,
  customer_location_id = 122,
  kontrak_id = 459,
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
  kontrak_id = 445,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2968;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 175,
  kontrak_id = 445,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2969;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 175,
  kontrak_id = 445,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2970;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 175,
  kontrak_id = 445,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2971;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_12,
  kontrak_id = 460,
  area_id = NULL,
  harga_sewa_bulanan = 26000000.0,
  on_hire_date = '2024-01-10',
  rate_changed_at = NOW()
WHERE no_unit = 5016;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_12,
  kontrak_id = 460,
  area_id = NULL,
  harga_sewa_bulanan = 26000000.0,
  on_hire_date = '2024-01-15',
  rate_changed_at = NOW()
WHERE no_unit = 5017;
UPDATE inventory_unit SET
  customer_id = 14,
  customer_location_id = 70,
  kontrak_id = 461,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5026;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 128,
  kontrak_id = 462,
  area_id = NULL,
  harga_sewa_bulanan = 17800000.0,
  on_hire_date = '2018-11-15',
  rate_changed_at = NOW()
WHERE no_unit = 2985;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 911,
  kontrak_id = 379,
  area_id = NULL,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2023-06-26',
  rate_changed_at = NOW()
WHERE no_unit = 2986;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = '2025-02-25',
  rate_changed_at = NOW()
WHERE no_unit = 5034;
UPDATE inventory_unit SET
  customer_id = 9,
  customer_location_id = 62,
  kontrak_id = 463,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2025-09-04',
  rate_changed_at = NOW()
WHERE no_unit = 2988;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5478;
UPDATE inventory_unit SET
  customer_id = 130,
  customer_location_id = 343,
  kontrak_id = 368,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2022-05-19',
  rate_changed_at = NOW()
WHERE no_unit = 2991;
UPDATE inventory_unit SET
  customer_id = 14,
  customer_location_id = 69,
  kontrak_id = 465,
  area_id = NULL,
  harga_sewa_bulanan = 19000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5039;
UPDATE inventory_unit SET
  customer_id = 48,
  customer_location_id = 939,
  kontrak_id = 466,
  area_id = NULL,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5041;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5479;
UPDATE inventory_unit SET
  customer_id = 121,
  customer_location_id = 328,
  kontrak_id = 467,
  area_id = NULL,
  harga_sewa_bulanan = 11350000.0,
  on_hire_date = '2022-01-04',
  rate_changed_at = NOW()
WHERE no_unit = 2995;
UPDATE inventory_unit SET
  customer_id = 121,
  customer_location_id = 328,
  kontrak_id = 467,
  area_id = NULL,
  harga_sewa_bulanan = 11350000.0,
  on_hire_date = '2021-12-30',
  rate_changed_at = NOW()
WHERE no_unit = 2996;
UPDATE inventory_unit SET
  customer_id = 121,
  customer_location_id = 328,
  kontrak_id = 467,
  area_id = NULL,
  harga_sewa_bulanan = 11350000.0,
  on_hire_date = '2021-12-28',
  rate_changed_at = NOW()
WHERE no_unit = 2997;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5480;
UPDATE inventory_unit SET
  customer_id = 121,
  customer_location_id = 328,
  kontrak_id = 467,
  area_id = NULL,
  harga_sewa_bulanan = 15850000.0,
  on_hire_date = '2021-12-24',
  rate_changed_at = NOW()
WHERE no_unit = 2999;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = 468,
  area_id = NULL,
  harga_sewa_bulanan = 46000000.0,
  on_hire_date = '2025-01-07',
  rate_changed_at = NOW()
WHERE no_unit = 5048;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 911,
  kontrak_id = 451,
  area_id = NULL,
  harga_sewa_bulanan = 13800000.0,
  on_hire_date = '2023-06-23',
  rate_changed_at = NOW()
WHERE no_unit = 3002;
UPDATE inventory_unit SET
  customer_id = 78,
  customer_location_id = 940,
  kontrak_id = 469,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3003;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 175,
  kontrak_id = 445,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3004;
UPDATE inventory_unit SET
  customer_id = 90,
  customer_location_id = @location_13,
  kontrak_id = 470,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3005;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
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
  customer_location_id = @location_12,
  kontrak_id = 460,
  area_id = NULL,
  harga_sewa_bulanan = 11400000.0,
  on_hire_date = '2022-07-29',
  rate_changed_at = NOW()
WHERE no_unit = 3009;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 55,
  kontrak_id = 386,
  area_id = 19,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3010;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5483;
UPDATE inventory_unit SET
  customer_id = 79,
  customer_location_id = 228,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 30500000.0,
  on_hire_date = '2025-05-07',
  rate_changed_at = NOW()
WHERE no_unit = 5062;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 5000000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 5064;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 5000000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 5065;
UPDATE inventory_unit SET
  customer_id = 65,
  customer_location_id = @location_14,
  kontrak_id = 471,
  area_id = NULL,
  harga_sewa_bulanan = 10500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3019;
UPDATE inventory_unit SET
  customer_id = 49,
  customer_location_id = 164,
  kontrak_id = 472,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2025-10-13',
  rate_changed_at = NOW()
WHERE no_unit = 3020;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 5000000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 5069;
UPDATE inventory_unit SET
  customer_id = 9,
  customer_location_id = 62,
  kontrak_id = 463,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2025-09-03',
  rate_changed_at = NOW()
WHERE no_unit = 3022;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 5000000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 5071;
UPDATE inventory_unit SET
  customer_id = 9,
  customer_location_id = 62,
  kontrak_id = 463,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2025-09-03',
  rate_changed_at = NOW()
WHERE no_unit = 3028;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = 943,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-08-02',
  rate_changed_at = NOW()
WHERE no_unit = 3029;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 5000000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 5079;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_7,
  kontrak_id = 416,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2021-07-31',
  rate_changed_at = NOW()
WHERE no_unit = 3034;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 51,
  kontrak_id = 386,
  area_id = NULL,
  harga_sewa_bulanan = 7150000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3035;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_7,
  kontrak_id = 416,
  area_id = 19,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = '2021-07-31',
  rate_changed_at = NOW()
WHERE no_unit = 3036;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 944,
  kontrak_id = 473,
  area_id = NULL,
  harga_sewa_bulanan = 5000000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 5082;
UPDATE inventory_unit SET
  customer_id = 148,
  customer_location_id = 387,
  kontrak_id = 474,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = '2025-03-25',
  rate_changed_at = NOW()
WHERE no_unit = 5083;
UPDATE inventory_unit SET
  customer_id = 64,
  customer_location_id = 913,
  kontrak_id = 475,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2025-11-27',
  rate_changed_at = NOW()
WHERE no_unit = 5085;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_6,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-10-03',
  rate_changed_at = NOW()
WHERE no_unit = 5895;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = 476,
  area_id = NULL,
  harga_sewa_bulanan = 7950000.0,
  on_hire_date = '2022-05-31',
  rate_changed_at = NOW()
WHERE no_unit = 3043;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = 476,
  area_id = NULL,
  harga_sewa_bulanan = 7950000.0,
  on_hire_date = '2022-05-31',
  rate_changed_at = NOW()
WHERE no_unit = 3044;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = 476,
  area_id = NULL,
  harga_sewa_bulanan = 7950000.0,
  on_hire_date = '2022-05-31',
  rate_changed_at = NOW()
WHERE no_unit = 3045;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = 477,
  area_id = NULL,
  harga_sewa_bulanan = 7950000.0,
  on_hire_date = '2022-05-31',
  rate_changed_at = NOW()
WHERE no_unit = 3046;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = 476,
  area_id = NULL,
  harga_sewa_bulanan = 9200000.0,
  on_hire_date = '2022-05-31',
  rate_changed_at = NOW()
WHERE no_unit = 3047;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = 476,
  area_id = NULL,
  harga_sewa_bulanan = 9200000.0,
  on_hire_date = '2022-05-31',
  rate_changed_at = NOW()
WHERE no_unit = 3048;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 911,
  kontrak_id = 478,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = '2025-11-17',
  rate_changed_at = NOW()
WHERE no_unit = 3049;
UPDATE inventory_unit SET
  customer_id = 162,
  customer_location_id = 404,
  kontrak_id = 479,
  area_id = NULL,
  harga_sewa_bulanan = 10250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1002;
UPDATE inventory_unit SET
  customer_id = 156,
  customer_location_id = 397,
  kontrak_id = 480,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = '2025-12-23',
  rate_changed_at = NOW()
WHERE no_unit = 1003;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = 476,
  area_id = NULL,
  harga_sewa_bulanan = 7450000.0,
  on_hire_date = '2022-06-08',
  rate_changed_at = NOW()
WHERE no_unit = 3050;
UPDATE inventory_unit SET
  customer_id = 140,
  customer_location_id = 360,
  kontrak_id = 481,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2022-06-01',
  rate_changed_at = NOW()
WHERE no_unit = 3053;
UPDATE inventory_unit SET
  customer_id = 186,
  customer_location_id = 431,
  kontrak_id = 482,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1006;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = 476,
  area_id = NULL,
  harga_sewa_bulanan = 7450000.0,
  on_hire_date = '2022-06-08',
  rate_changed_at = NOW()
WHERE no_unit = 3051;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = 945,
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
  kontrak_id = 386,
  area_id = 19,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3057;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 54,
  kontrak_id = 386,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3059;
UPDATE inventory_unit SET
  customer_id = 174,
  customer_location_id = 416,
  kontrak_id = 483,
  area_id = NULL,
  harga_sewa_bulanan = 12800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5107;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_12,
  kontrak_id = 460,
  area_id = NULL,
  harga_sewa_bulanan = 11400000.0,
  on_hire_date = '2022-07-29',
  rate_changed_at = NOW()
WHERE no_unit = 3061;
UPDATE inventory_unit SET
  customer_id = 174,
  customer_location_id = 416,
  kontrak_id = 483,
  area_id = NULL,
  harga_sewa_bulanan = 12800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5108;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = 476,
  area_id = NULL,
  harga_sewa_bulanan = 7950000.0,
  on_hire_date = '2022-06-13',
  rate_changed_at = NOW()
WHERE no_unit = 3063;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = 476,
  area_id = NULL,
  harga_sewa_bulanan = 7950000.0,
  on_hire_date = '2022-06-13',
  rate_changed_at = NOW()
WHERE no_unit = 3064;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_15,
  kontrak_id = 484,
  area_id = NULL,
  harga_sewa_bulanan = 8200000.0,
  on_hire_date = '2022-07-06',
  rate_changed_at = NOW()
WHERE no_unit = 3065;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = 476,
  area_id = NULL,
  harga_sewa_bulanan = 7950000.0,
  on_hire_date = '2022-06-08',
  rate_changed_at = NOW()
WHERE no_unit = 3066;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = 476,
  area_id = NULL,
  harga_sewa_bulanan = 7950000.0,
  on_hire_date = '2022-06-13',
  rate_changed_at = NOW()
WHERE no_unit = 3067;
UPDATE inventory_unit SET
  customer_id = 50,
  customer_location_id = 947,
  kontrak_id = 485,
  area_id = NULL,
  harga_sewa_bulanan = 7950000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3068;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = 476,
  area_id = NULL,
  harga_sewa_bulanan = 7950000.0,
  on_hire_date = '2022-06-13',
  rate_changed_at = NOW()
WHERE no_unit = 3069;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = 476,
  area_id = NULL,
  harga_sewa_bulanan = 7950000.0,
  on_hire_date = '2022-06-09',
  rate_changed_at = NOW()
WHERE no_unit = 3070;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_12,
  kontrak_id = 460,
  area_id = NULL,
  harga_sewa_bulanan = 6950000.0,
  on_hire_date = '2022-07-25',
  rate_changed_at = NOW()
WHERE no_unit = 3071;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_12,
  kontrak_id = 460,
  area_id = NULL,
  harga_sewa_bulanan = 6950000.0,
  on_hire_date = '2022-07-25',
  rate_changed_at = NOW()
WHERE no_unit = 3072;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = 476,
  area_id = NULL,
  harga_sewa_bulanan = 7950000.0,
  on_hire_date = '2022-06-13',
  rate_changed_at = NOW()
WHERE no_unit = 3073;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_16,
  kontrak_id = 486,
  area_id = NULL,
  harga_sewa_bulanan = 6950000.0,
  on_hire_date = '2022-07-25',
  rate_changed_at = NOW()
WHERE no_unit = 3074;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = 476,
  area_id = NULL,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2022-06-30',
  rate_changed_at = NOW()
WHERE no_unit = 3075;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = 476,
  area_id = NULL,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2022-06-30',
  rate_changed_at = NOW()
WHERE no_unit = 3076;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_15,
  kontrak_id = 484,
  area_id = NULL,
  harga_sewa_bulanan = 7600000.0,
  on_hire_date = '2022-06-30',
  rate_changed_at = NOW()
WHERE no_unit = 3077;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_15,
  kontrak_id = 484,
  area_id = NULL,
  harga_sewa_bulanan = 7700000.0,
  on_hire_date = '2022-07-06',
  rate_changed_at = NOW()
WHERE no_unit = 3078;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_16,
  kontrak_id = 486,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = '2022-07-25',
  rate_changed_at = NOW()
WHERE no_unit = 3079;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_16,
  kontrak_id = 486,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = '2022-07-25',
  rate_changed_at = NOW()
WHERE no_unit = 3080;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_16,
  kontrak_id = 486,
  area_id = NULL,
  harga_sewa_bulanan = 6950000.0,
  on_hire_date = '2022-07-25',
  rate_changed_at = NOW()
WHERE no_unit = 3081;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_16,
  kontrak_id = 486,
  area_id = NULL,
  harga_sewa_bulanan = 6950000.0,
  on_hire_date = '2022-07-25',
  rate_changed_at = NOW()
WHERE no_unit = 3082;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_12,
  kontrak_id = 460,
  area_id = NULL,
  harga_sewa_bulanan = 8900000.0,
  on_hire_date = '2022-07-25',
  rate_changed_at = NOW()
WHERE no_unit = 3083;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 20,
  kontrak_id = 487,
  area_id = 2,
  harga_sewa_bulanan = 22030000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 5123;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 18,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 22030000.0,
  on_hire_date = '2024-01-03',
  rate_changed_at = NOW()
WHERE no_unit = 5124;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 19,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 22030000.0,
  on_hire_date = '2024-01-06',
  rate_changed_at = NOW()
WHERE no_unit = 5125;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 15,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 21030000.0,
  on_hire_date = '2023-12-28',
  rate_changed_at = NOW()
WHERE no_unit = 5126;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 17,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 22030000.0,
  on_hire_date = '2023-12-21',
  rate_changed_at = NOW()
WHERE no_unit = 5127;

-- Processed 300/2178 units

UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 14,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 22030000.0,
  on_hire_date = '2023-12-30',
  rate_changed_at = NOW()
WHERE no_unit = 5128;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = 949,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2023-08-23',
  rate_changed_at = NOW()
WHERE no_unit = 3090;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 9,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 25780000.0,
  on_hire_date = '2025-08-12',
  rate_changed_at = NOW()
WHERE no_unit = 5130;
UPDATE inventory_unit SET
  customer_id = 80,
  customer_location_id = @location_10,
  kontrak_id = 488,
  area_id = NULL,
  harga_sewa_bulanan = 57000000.0,
  on_hire_date = '2024-01-30',
  rate_changed_at = NOW()
WHERE no_unit = 5134;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 419,
  kontrak_id = 402,
  area_id = NULL,
  harga_sewa_bulanan = 23500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1045;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 123,
  kontrak_id = 370,
  area_id = 17,
  harga_sewa_bulanan = 7700.0,
  on_hire_date = '2025-07-30',
  rate_changed_at = NOW()
WHERE no_unit = 5136;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 237,
  kontrak_id = 489,
  area_id = NULL,
  harga_sewa_bulanan = 12900000.0,
  on_hire_date = '2022-01-10',
  rate_changed_at = NOW()
WHERE no_unit = 5137;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 237,
  kontrak_id = 490,
  area_id = NULL,
  harga_sewa_bulanan = 12900000.0,
  on_hire_date = '2023-07-12',
  rate_changed_at = NOW()
WHERE no_unit = 5139;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 237,
  kontrak_id = 490,
  area_id = NULL,
  harga_sewa_bulanan = 12900000.0,
  on_hire_date = '2023-07-04',
  rate_changed_at = NOW()
WHERE no_unit = 5140;
UPDATE inventory_unit SET
  customer_id = 177,
  customer_location_id = 422,
  kontrak_id = 376,
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
  kontrak_id = 491,
  area_id = NULL,
  harga_sewa_bulanan = 11330000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1052;
UPDATE inventory_unit SET
  customer_id = 140,
  customer_location_id = 360,
  kontrak_id = 481,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2022-06-01',
  rate_changed_at = NOW()
WHERE no_unit = 3099;
UPDATE inventory_unit SET
  customer_id = 140,
  customer_location_id = 360,
  kontrak_id = 481,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2022-06-01',
  rate_changed_at = NOW()
WHERE no_unit = 3100;
UPDATE inventory_unit SET
  customer_id = 11,
  customer_location_id = 919,
  kontrak_id = 403,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5142;
UPDATE inventory_unit SET
  customer_id = 72,
  customer_location_id = 218,
  kontrak_id = 492,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5148;
UPDATE inventory_unit SET
  customer_id = 102,
  customer_location_id = 290,
  kontrak_id = 493,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5151;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 44,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = '2023-11-16',
  rate_changed_at = NOW()
WHERE no_unit = 5153;
UPDATE inventory_unit SET
  customer_id = 140,
  customer_location_id = 360,
  kontrak_id = 481,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2022-06-01',
  rate_changed_at = NOW()
WHERE no_unit = 3107;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_12,
  kontrak_id = 460,
  area_id = NULL,
  harga_sewa_bulanan = 11400000.0,
  on_hire_date = '2022-07-29',
  rate_changed_at = NOW()
WHERE no_unit = 3108;
UPDATE inventory_unit SET
  customer_id = 7,
  customer_location_id = @location_11,
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
  kontrak_id = 400,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-06-07',
  rate_changed_at = NOW()
WHERE no_unit = 5157;
UPDATE inventory_unit SET
  customer_id = 168,
  customer_location_id = 410,
  kontrak_id = 494,
  area_id = NULL,
  harga_sewa_bulanan = 8750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3112;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_12,
  kontrak_id = 460,
  area_id = NULL,
  harga_sewa_bulanan = 10900000.0,
  on_hire_date = '2022-07-29',
  rate_changed_at = NOW()
WHERE no_unit = 3113;
UPDATE inventory_unit SET
  customer_id = 43,
  customer_location_id = 121,
  kontrak_id = 491,
  area_id = NULL,
  harga_sewa_bulanan = 9900000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3114;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = 950,
  kontrak_id = @contract_2,
  area_id = NULL,
  harga_sewa_bulanan = 13500000.0,
  on_hire_date = '2023-01-19',
  rate_changed_at = NOW()
WHERE no_unit = 3115;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = 951,
  kontrak_id = 496,
  area_id = NULL,
  harga_sewa_bulanan = 13500000.0,
  on_hire_date = '2023-01-19',
  rate_changed_at = NOW()
WHERE no_unit = 3116;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = 497,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3117;
UPDATE inventory_unit SET
  customer_id = 43,
  customer_location_id = 121,
  kontrak_id = 491,
  area_id = NULL,
  harga_sewa_bulanan = 10550000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3118;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 952,
  kontrak_id = 498,
  area_id = 26,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3119;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = 400,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-06-07',
  rate_changed_at = NOW()
WHERE no_unit = 5159;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = 951,
  kontrak_id = 496,
  area_id = NULL,
  harga_sewa_bulanan = 13500000.0,
  on_hire_date = '2023-01-19',
  rate_changed_at = NOW()
WHERE no_unit = 3121;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 953,
  kontrak_id = 499,
  area_id = 26,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3122;
UPDATE inventory_unit SET
  customer_id = 43,
  customer_location_id = 121,
  kontrak_id = 491,
  area_id = NULL,
  harga_sewa_bulanan = 9900000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3123;
UPDATE inventory_unit SET
  customer_id = 64,
  customer_location_id = 913,
  kontrak_id = 500,
  area_id = NULL,
  harga_sewa_bulanan = 11200000.0,
  on_hire_date = '2025-10-15',
  rate_changed_at = NOW()
WHERE no_unit = 5164;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 251,
  kontrak_id = 400,
  area_id = NULL,
  harga_sewa_bulanan = 11950000.0,
  on_hire_date = '2024-06-07',
  rate_changed_at = NOW()
WHERE no_unit = 5165;
UPDATE inventory_unit SET
  customer_id = 127,
  customer_location_id = 338,
  kontrak_id = 501,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2023-07-27',
  rate_changed_at = NOW()
WHERE no_unit = 3126;
UPDATE inventory_unit SET
  customer_id = 148,
  customer_location_id = 388,
  kontrak_id = 502,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = '2025-08-07',
  rate_changed_at = NOW()
WHERE no_unit = 5167;
UPDATE inventory_unit SET
  customer_id = 148,
  customer_location_id = 388,
  kontrak_id = 502,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = '2025-08-06',
  rate_changed_at = NOW()
WHERE no_unit = 5172;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 419,
  kontrak_id = 402,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5173;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = 951,
  kontrak_id = 496,
  area_id = NULL,
  harga_sewa_bulanan = 13500000.0,
  on_hire_date = '2023-01-19',
  rate_changed_at = NOW()
WHERE no_unit = 3133;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = @contract_3,
  area_id = 26,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3134;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 504,
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
  kontrak_id = 505,
  area_id = NULL,
  harga_sewa_bulanan = 30000000.0,
  on_hire_date = '2025-02-14',
  rate_changed_at = NOW()
WHERE no_unit = 3138;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 419,
  kontrak_id = 402,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5185;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 237,
  kontrak_id = 489,
  area_id = NULL,
  harga_sewa_bulanan = 13400000.0,
  on_hire_date = '2022-01-11',
  rate_changed_at = NOW()
WHERE no_unit = 3140;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 237,
  kontrak_id = 489,
  area_id = NULL,
  harga_sewa_bulanan = 13400000.0,
  on_hire_date = '2022-08-12',
  rate_changed_at = NOW()
WHERE no_unit = 3141;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = 223,
  kontrak_id = 506,
  area_id = NULL,
  harga_sewa_bulanan = 12400000.0,
  on_hire_date = '2025-05-13',
  rate_changed_at = NOW()
WHERE no_unit = 5186;
UPDATE inventory_unit SET
  customer_id = 128,
  customer_location_id = 339,
  kontrak_id = 373,
  area_id = 19,
  harga_sewa_bulanan = 9900000.0,
  on_hire_date = '2025-01-22',
  rate_changed_at = NOW()
WHERE no_unit = 5187;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_12,
  kontrak_id = 460,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2022-08-06',
  rate_changed_at = NOW()
WHERE no_unit = 3144;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_12,
  kontrak_id = 460,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2022-08-06',
  rate_changed_at = NOW()
WHERE no_unit = 3145;
UPDATE inventory_unit SET
  customer_id = 69,
  customer_location_id = 213,
  kontrak_id = 507,
  area_id = NULL,
  harga_sewa_bulanan = 17500000.0,
  on_hire_date = '2024-07-31',
  rate_changed_at = NOW()
WHERE no_unit = 5190;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 56,
  kontrak_id = 386,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3147;
UPDATE inventory_unit SET
  customer_id = 21,
  customer_location_id = 933,
  kontrak_id = 508,
  area_id = NULL,
  harga_sewa_bulanan = 24000000.0,
  on_hire_date = '2022-03-31',
  rate_changed_at = NOW()
WHERE no_unit = 5192;
UPDATE inventory_unit SET
  customer_id = 170,
  customer_location_id = 412,
  kontrak_id = 509,
  area_id = 33,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5195;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_6,
  kontrak_id = 510,
  area_id = 19,
  harga_sewa_bulanan = 16000000.0,
  on_hire_date = '2024-12-03',
  rate_changed_at = NOW()
WHERE no_unit = 5196;
UPDATE inventory_unit SET
  customer_id = 170,
  customer_location_id = 954,
  kontrak_id = 483,
  area_id = 33,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5197;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_6,
  kontrak_id = 511,
  area_id = 19,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = '2024-11-11',
  rate_changed_at = NOW()
WHERE no_unit = 5198;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_5,
  kontrak_id = 512,
  area_id = 19,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5199;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_5,
  kontrak_id = 513,
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
  kontrak_id = 514,
  area_id = 26,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5205;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = 448,
  area_id = 26,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-05-15',
  rate_changed_at = NOW()
WHERE no_unit = 5206;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = 448,
  area_id = 26,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-11-29',
  rate_changed_at = NOW()
WHERE no_unit = 5208;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 249,
  kontrak_id = 400,
  area_id = NULL,
  harga_sewa_bulanan = 11950000.0,
  on_hire_date = '2024-06-14',
  rate_changed_at = NOW()
WHERE no_unit = 5209;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 237,
  kontrak_id = 515,
  area_id = NULL,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = '2022-09-22',
  rate_changed_at = NOW()
WHERE no_unit = 3162;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = 232,
  kontrak_id = 516,
  area_id = NULL,
  harga_sewa_bulanan = 36500000.0,
  on_hire_date = '2025-12-05',
  rate_changed_at = NOW()
WHERE no_unit = 3163;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 251,
  kontrak_id = 400,
  area_id = NULL,
  harga_sewa_bulanan = 11950000.0,
  on_hire_date = '2024-06-07',
  rate_changed_at = NOW()
WHERE no_unit = 5210;
UPDATE inventory_unit SET
  customer_id = 73,
  customer_location_id = 220,
  kontrak_id = 450,
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
  kontrak_id = 517,
  area_id = NULL,
  harga_sewa_bulanan = 13500000.0,
  on_hire_date = '2025-04-26',
  rate_changed_at = NOW()
WHERE no_unit = 5213;
UPDATE inventory_unit SET
  customer_id = 100,
  customer_location_id = @location_17,
  kontrak_id = 518,
  area_id = NULL,
  harga_sewa_bulanan = 9500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3170;
UPDATE inventory_unit SET
  customer_id = 100,
  customer_location_id = @location_17,
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
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 14900000.0,
  on_hire_date = '2024-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 5218;
UPDATE inventory_unit SET
  customer_id = 99,
  customer_location_id = 927,
  kontrak_id = 519,
  area_id = NULL,
  harga_sewa_bulanan = 7800000.0,
  on_hire_date = '2025-11-21',
  rate_changed_at = NOW()
WHERE no_unit = 3174;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 14900000.0,
  on_hire_date = '2024-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 5219;
UPDATE inventory_unit SET
  customer_id = 34,
  customer_location_id = 106,
  kontrak_id = 520,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3176;
UPDATE inventory_unit SET
  customer_id = 174,
  customer_location_id = 416,
  kontrak_id = 483,
  area_id = NULL,
  harga_sewa_bulanan = 12800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5109;
UPDATE inventory_unit SET
  customer_id = 97,
  customer_location_id = 283,
  kontrak_id = 368,
  area_id = NULL,
  harga_sewa_bulanan = 13500000.0,
  on_hire_date = '2024-11-12',
  rate_changed_at = NOW()
WHERE no_unit = 5220;
UPDATE inventory_unit SET
  customer_id = 80,
  customer_location_id = @location_10,
  kontrak_id = 421,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = '2023-02-13',
  rate_changed_at = NOW()
WHERE no_unit = 3179;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = 956,
  kontrak_id = 521,
  area_id = NULL,
  harga_sewa_bulanan = 38900000.0,
  on_hire_date = '2025-05-30',
  rate_changed_at = NOW()
WHERE no_unit = 3180;
UPDATE inventory_unit SET
  customer_id = 80,
  customer_location_id = @location_10,
  kontrak_id = 421,
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
  customer_location_id = 913,
  kontrak_id = 517,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2025-04-28',
  rate_changed_at = NOW()
WHERE no_unit = 5224;
UPDATE inventory_unit SET
  customer_id = 49,
  customer_location_id = @location_18,
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
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 14900000.0,
  on_hire_date = '2024-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 5227;
UPDATE inventory_unit SET
  customer_id = 49,
  customer_location_id = @location_18,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = '2024-07-03',
  rate_changed_at = NOW()
WHERE no_unit = 5229;
UPDATE inventory_unit SET
  customer_id = 7,
  customer_location_id = @location_11,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 27000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3188;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 14900000.0,
  on_hire_date = '2024-11-22',
  rate_changed_at = NOW()
WHERE no_unit = 5230;
UPDATE inventory_unit SET
  customer_id = 174,
  customer_location_id = 416,
  kontrak_id = 522,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5231;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = 523,
  area_id = NULL,
  harga_sewa_bulanan = 24000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5583;
UPDATE inventory_unit SET
  customer_id = 174,
  customer_location_id = 416,
  kontrak_id = 483,
  area_id = NULL,
  harga_sewa_bulanan = 6800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5112;
UPDATE inventory_unit SET
  customer_id = 34,
  customer_location_id = 106,
  kontrak_id = 520,
  area_id = NULL,
  harga_sewa_bulanan = 16600000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3193;

-- Processed 400/2178 units

UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = 524,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3194;
UPDATE inventory_unit SET
  customer_id = 37,
  customer_location_id = 110,
  kontrak_id = 525,
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
  kontrak_id = 483,
  area_id = NULL,
  harga_sewa_bulanan = 6800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5113;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 251,
  kontrak_id = 400,
  area_id = NULL,
  harga_sewa_bulanan = 11950000.0,
  on_hire_date = '2024-10-22',
  rate_changed_at = NOW()
WHERE no_unit = 5244;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 251,
  kontrak_id = 400,
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
  kontrak_id = 483,
  area_id = NULL,
  harga_sewa_bulanan = 6800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5114;
UPDATE inventory_unit SET
  customer_id = 94,
  customer_location_id = 275,
  kontrak_id = 373,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2022-03-23',
  rate_changed_at = NOW()
WHERE no_unit = 3203;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 958,
  kontrak_id = 526,
  area_id = NULL,
  harga_sewa_bulanan = 27500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5248;
UPDATE inventory_unit SET
  customer_id = 174,
  customer_location_id = 416,
  kontrak_id = 483,
  area_id = NULL,
  harga_sewa_bulanan = 6800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5115;
UPDATE inventory_unit SET
  customer_id = 117,
  customer_location_id = 321,
  kontrak_id = 368,
  area_id = NULL,
  harga_sewa_bulanan = 24000000.0,
  on_hire_date = '2024-08-29',
  rate_changed_at = NOW()
WHERE no_unit = 5257;
UPDATE inventory_unit SET
  customer_id = 117,
  customer_location_id = 321,
  kontrak_id = 368,
  area_id = NULL,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = '2024-08-29',
  rate_changed_at = NOW()
WHERE no_unit = 5258;
UPDATE inventory_unit SET
  customer_id = 117,
  customer_location_id = 321,
  kontrak_id = 368,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2024-08-29',
  rate_changed_at = NOW()
WHERE no_unit = 5259;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_15,
  kontrak_id = 484,
  area_id = NULL,
  harga_sewa_bulanan = 11900000.0,
  on_hire_date = '2022-11-02',
  rate_changed_at = NOW()
WHERE no_unit = 3212;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_15,
  kontrak_id = 484,
  area_id = NULL,
  harga_sewa_bulanan = 10900000.0,
  on_hire_date = '2022-10-28',
  rate_changed_at = NOW()
WHERE no_unit = 3213;
UPDATE inventory_unit SET
  customer_id = 174,
  customer_location_id = 416,
  kontrak_id = 483,
  area_id = NULL,
  harga_sewa_bulanan = 6800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5116;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 237,
  kontrak_id = 489,
  area_id = NULL,
  harga_sewa_bulanan = 12900000.0,
  on_hire_date = '2022-02-10',
  rate_changed_at = NOW()
WHERE no_unit = 3215;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 237,
  kontrak_id = 489,
  area_id = NULL,
  harga_sewa_bulanan = 12900000.0,
  on_hire_date = '2022-02-26',
  rate_changed_at = NOW()
WHERE no_unit = 3216;
UPDATE inventory_unit SET
  customer_id = 174,
  customer_location_id = 416,
  kontrak_id = 483,
  area_id = NULL,
  harga_sewa_bulanan = 6800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5117;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 237,
  kontrak_id = 489,
  area_id = NULL,
  harga_sewa_bulanan = 12900000.0,
  on_hire_date = '2022-03-05',
  rate_changed_at = NOW()
WHERE no_unit = 3218;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = 956,
  kontrak_id = 527,
  area_id = NULL,
  harga_sewa_bulanan = 60000000.0,
  on_hire_date = '2022-12-26',
  rate_changed_at = NOW()
WHERE no_unit = 5260;
UPDATE inventory_unit SET
  customer_id = 127,
  customer_location_id = 338,
  kontrak_id = 528,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2024-08-14',
  rate_changed_at = NOW()
WHERE no_unit = 5261;
UPDATE inventory_unit SET
  customer_id = 14,
  customer_location_id = 68,
  kontrak_id = 529,
  area_id = NULL,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5262;
UPDATE inventory_unit SET
  customer_id = 27,
  customer_location_id = 87,
  kontrak_id = 530,
  area_id = NULL,
  harga_sewa_bulanan = 31500000.0,
  on_hire_date = '2024-02-05',
  rate_changed_at = NOW()
WHERE no_unit = 5118;
UPDATE inventory_unit SET
  customer_id = 14,
  customer_location_id = 959,
  kontrak_id = 529,
  area_id = NULL,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5263;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_5,
  kontrak_id = 531,
  area_id = 19,
  harga_sewa_bulanan = 21500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5268;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 390,
  area_id = 26,
  harga_sewa_bulanan = 22500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5272;
UPDATE inventory_unit SET
  customer_id = 27,
  customer_location_id = 89,
  kontrak_id = 532,
  area_id = 19,
  harga_sewa_bulanan = 17000000.0,
  on_hire_date = '2024-10-30',
  rate_changed_at = NOW()
WHERE no_unit = 5274;
UPDATE inventory_unit SET
  customer_id = 27,
  customer_location_id = 88,
  kontrak_id = 533,
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
  customer_location_id = @location_10,
  kontrak_id = 421,
  area_id = NULL,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = '2023-02-15',
  rate_changed_at = NOW()
WHERE no_unit = 3229;
UPDATE inventory_unit SET
  customer_id = 93,
  customer_location_id = 273,
  kontrak_id = 534,
  area_id = NULL,
  harga_sewa_bulanan = 16000000.0,
  on_hire_date = '2025-11-13',
  rate_changed_at = NOW()
WHERE no_unit = 3230;
UPDATE inventory_unit SET
  customer_id = 24,
  customer_location_id = 909,
  kontrak_id = 535,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2024-11-11',
  rate_changed_at = NOW()
WHERE no_unit = 5279;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = 400,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-09-30',
  rate_changed_at = NOW()
WHERE no_unit = 5280;
UPDATE inventory_unit SET
  customer_id = 27,
  customer_location_id = 88,
  kontrak_id = 533,
  area_id = NULL,
  harga_sewa_bulanan = 31500000.0,
  on_hire_date = '2024-02-05',
  rate_changed_at = NOW()
WHERE no_unit = 5120;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_19,
  kontrak_id = 536,
  area_id = 19,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3234;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_19,
  kontrak_id = 536,
  area_id = 19,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3235;
UPDATE inventory_unit SET
  customer_id = 24,
  customer_location_id = 961,
  kontrak_id = 537,
  area_id = NULL,
  harga_sewa_bulanan = 22000000.0,
  on_hire_date = '2025-12-15',
  rate_changed_at = NOW()
WHERE no_unit = 5281;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_19,
  kontrak_id = 536,
  area_id = 19,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3237;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_19,
  kontrak_id = 536,
  area_id = 19,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3238;
UPDATE inventory_unit SET
  customer_id = 27,
  customer_location_id = 87,
  kontrak_id = 530,
  area_id = NULL,
  harga_sewa_bulanan = 31500000.0,
  on_hire_date = '2024-02-05',
  rate_changed_at = NOW()
WHERE no_unit = 5121;
UPDATE inventory_unit SET
  customer_id = 30,
  customer_location_id = 100,
  kontrak_id = 538,
  area_id = NULL,
  harga_sewa_bulanan = 16200000.0,
  on_hire_date = '2025-12-20',
  rate_changed_at = NOW()
WHERE no_unit = 5282;
UPDATE inventory_unit SET
  customer_id = 96,
  customer_location_id = 281,
  kontrak_id = 539,
  area_id = NULL,
  harga_sewa_bulanan = 11300000.0,
  on_hire_date = '2025-03-26',
  rate_changed_at = NOW()
WHERE no_unit = 5283;
UPDATE inventory_unit SET
  customer_id = 96,
  customer_location_id = 962,
  kontrak_id = 540,
  area_id = NULL,
  harga_sewa_bulanan = 11750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5284;
UPDATE inventory_unit SET
  customer_id = 14,
  customer_location_id = 71,
  kontrak_id = 541,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3243;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = @location_20,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 22030000.0,
  on_hire_date = '2023-12-28',
  rate_changed_at = NOW()
WHERE no_unit = 5122;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = 964,
  kontrak_id = NULL,
  area_id = 13,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-02-08',
  rate_changed_at = NOW()
WHERE no_unit = 3245;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 417,
  area_id = 35,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = '2025-12-10',
  rate_changed_at = NOW()
WHERE no_unit = 5285;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = 964,
  kontrak_id = NULL,
  area_id = 13,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-02-08',
  rate_changed_at = NOW()
WHERE no_unit = 3247;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 123,
  kontrak_id = 370,
  area_id = 17,
  harga_sewa_bulanan = 7700.0,
  on_hire_date = '2025-07-30',
  rate_changed_at = NOW()
WHERE no_unit = 5288;
UPDATE inventory_unit SET
  customer_id = 21,
  customer_location_id = 933,
  kontrak_id = 542,
  area_id = 18,
  harga_sewa_bulanan = 12900000.0,
  on_hire_date = '2024-08-30',
  rate_changed_at = NOW()
WHERE no_unit = 5289;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 965,
  kontrak_id = 543,
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
  kontrak_id = 450,
  area_id = NULL,
  harga_sewa_bulanan = 32700000.0,
  on_hire_date = '2021-12-22',
  rate_changed_at = NOW()
WHERE no_unit = 3252;
UPDATE inventory_unit SET
  customer_id = 96,
  customer_location_id = 962,
  kontrak_id = 540,
  area_id = NULL,
  harga_sewa_bulanan = 11750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5293;
UPDATE inventory_unit SET
  customer_id = 96,
  customer_location_id = 962,
  kontrak_id = 540,
  area_id = NULL,
  harga_sewa_bulanan = 11750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5294;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = 966,
  kontrak_id = 544,
  area_id = NULL,
  harga_sewa_bulanan = 5850000.0,
  on_hire_date = '2023-01-18',
  rate_changed_at = NOW()
WHERE no_unit = 3255;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = 966,
  kontrak_id = 544,
  area_id = NULL,
  harga_sewa_bulanan = 5850000.0,
  on_hire_date = '2023-01-18',
  rate_changed_at = NOW()
WHERE no_unit = 3256;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = 951,
  kontrak_id = @contract_2,
  area_id = NULL,
  harga_sewa_bulanan = 5850000.0,
  on_hire_date = '2023-01-18',
  rate_changed_at = NOW()
WHERE no_unit = 3257;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = 966,
  kontrak_id = 544,
  area_id = NULL,
  harga_sewa_bulanan = 5850000.0,
  on_hire_date = '2023-01-18',
  rate_changed_at = NOW()
WHERE no_unit = 3258;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = 951,
  kontrak_id = 496,
  area_id = NULL,
  harga_sewa_bulanan = 5850000.0,
  on_hire_date = '2023-01-18',
  rate_changed_at = NOW()
WHERE no_unit = 3259;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = 967,
  kontrak_id = @contract_2,
  area_id = NULL,
  harga_sewa_bulanan = 5850000.0,
  on_hire_date = '2023-01-19',
  rate_changed_at = NOW()
WHERE no_unit = 3260;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = 967,
  kontrak_id = 496,
  area_id = NULL,
  harga_sewa_bulanan = 5850000.0,
  on_hire_date = '2023-01-18',
  rate_changed_at = NOW()
WHERE no_unit = 3261;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_6,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2025-09-05',
  rate_changed_at = NOW()
WHERE no_unit = 5301;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = 967,
  kontrak_id = 496,
  area_id = NULL,
  harga_sewa_bulanan = 5850000.0,
  on_hire_date = '2023-01-19',
  rate_changed_at = NOW()
WHERE no_unit = 3263;
UPDATE inventory_unit SET
  customer_id = 27,
  customer_location_id = 86,
  kontrak_id = 399,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2024-12-04',
  rate_changed_at = NOW()
WHERE no_unit = 5305;
UPDATE inventory_unit SET
  customer_id = 214,
  customer_location_id = 968,
  kontrak_id = 545,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2025-12-16',
  rate_changed_at = NOW()
WHERE no_unit = 5306;
UPDATE inventory_unit SET
  customer_id = 131,
  customer_location_id = 344,
  kontrak_id = 543,
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
  customer_location_id = 928,
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
  kontrak_id = 546,
  area_id = NULL,
  harga_sewa_bulanan = 9500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5312;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_6,
  kontrak_id = 510,
  area_id = 19,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2024-12-03',
  rate_changed_at = NOW()
WHERE no_unit = 5314;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = 448,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2025-01-08',
  rate_changed_at = NOW()
WHERE no_unit = 5315;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = 448,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2025-01-08',
  rate_changed_at = NOW()
WHERE no_unit = 5316;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 417,
  area_id = 35,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2025-12-10',
  rate_changed_at = NOW()
WHERE no_unit = 5318;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = 547,
  area_id = 18,
  harga_sewa_bulanan = 5950000.0,
  on_hire_date = '2025-02-07',
  rate_changed_at = NOW()
WHERE no_unit = 5321;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = 548,
  area_id = 18,
  harga_sewa_bulanan = 5950000.0,
  on_hire_date = '2025-01-07',
  rate_changed_at = NOW()
WHERE no_unit = 5322;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 16,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 22030000.0,
  on_hire_date = '2023-12-30',
  rate_changed_at = NOW()
WHERE no_unit = 5129;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_6,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2025-09-05',
  rate_changed_at = NOW()
WHERE no_unit = 5324;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 123,
  kontrak_id = 370,
  area_id = 17,
  harga_sewa_bulanan = 7700.0,
  on_hire_date = '2025-07-29',
  rate_changed_at = NOW()
WHERE no_unit = 5325;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 123,
  kontrak_id = 370,
  area_id = 17,
  harga_sewa_bulanan = 7700.0,
  on_hire_date = '2025-07-29',
  rate_changed_at = NOW()
WHERE no_unit = 5327;
UPDATE inventory_unit SET
  customer_id = 117,
  customer_location_id = 321,
  kontrak_id = 368,
  area_id = NULL,
  harga_sewa_bulanan = 87000000.0,
  on_hire_date = '2024-12-03',
  rate_changed_at = NOW()
WHERE no_unit = 5328;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 247,
  kontrak_id = 432,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = '2024-10-04',
  rate_changed_at = NOW()
WHERE no_unit = 5330;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 5331;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 5332;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 5333;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 5334;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 549,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-02-02',
  rate_changed_at = NOW()
WHERE no_unit = 3288;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 5335;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 5336;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 5337;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 5338;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
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
  customer_location_id = 911,
  kontrak_id = 379,
  area_id = NULL,
  harga_sewa_bulanan = 5500000.0,
  on_hire_date = '2023-05-31',
  rate_changed_at = NOW()
WHERE no_unit = 3295;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 550,
  area_id = 26,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3296;

-- Processed 500/2178 units

UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 419,
  kontrak_id = 402,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3297;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 420,
  kontrak_id = 402,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3298;
UPDATE inventory_unit SET
  customer_id = 121,
  customer_location_id = 328,
  kontrak_id = 467,
  area_id = NULL,
  harga_sewa_bulanan = 11350000.0,
  on_hire_date = '2022-09-07',
  rate_changed_at = NOW()
WHERE no_unit = 3299;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-09-20',
  rate_changed_at = NOW()
WHERE no_unit = 5340;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = 951,
  kontrak_id = @contract_2,
  area_id = NULL,
  harga_sewa_bulanan = 27000000.0,
  on_hire_date = '2024-09-27',
  rate_changed_at = NOW()
WHERE no_unit = 5341;
UPDATE inventory_unit SET
  customer_id = 27,
  customer_location_id = 89,
  kontrak_id = 551,
  area_id = 19,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = '2024-09-27',
  rate_changed_at = NOW()
WHERE no_unit = 5342;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_21,
  kontrak_id = 552,
  area_id = 33,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3303;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 244,
  kontrak_id = 400,
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
  customer_location_id = 970,
  kontrak_id = 373,
  area_id = 19,
  harga_sewa_bulanan = 9900000.0,
  on_hire_date = '2025-03-25',
  rate_changed_at = NOW()
WHERE no_unit = 5345;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 378,
  kontrak_id = 553,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1259;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_21,
  kontrak_id = 552,
  area_id = 33,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3308;
UPDATE inventory_unit SET
  customer_id = 27,
  customer_location_id = 87,
  kontrak_id = 554,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-01-07',
  rate_changed_at = NOW()
WHERE no_unit = 5348;
UPDATE inventory_unit SET
  customer_id = 27,
  customer_location_id = 87,
  kontrak_id = 554,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-01-17',
  rate_changed_at = NOW()
WHERE no_unit = 5349;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 420,
  kontrak_id = 402,
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
  kontrak_id = 555,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = '2025-12-04',
  rate_changed_at = NOW()
WHERE no_unit = 5352;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_5,
  kontrak_id = 556,
  area_id = 19,
  harga_sewa_bulanan = 21500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3314;
UPDATE inventory_unit SET
  customer_id = 93,
  customer_location_id = 273,
  kontrak_id = 555,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = '2025-12-04',
  rate_changed_at = NOW()
WHERE no_unit = 5354;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_5,
  kontrak_id = 557,
  area_id = 19,
  harga_sewa_bulanan = 22500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3316;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 379,
  kontrak_id = 558,
  area_id = 19,
  harga_sewa_bulanan = 10500000.0,
  on_hire_date = '2023-09-08',
  rate_changed_at = NOW()
WHERE no_unit = 3317;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = 400,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-09-27',
  rate_changed_at = NOW()
WHERE no_unit = 5362;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 559,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3319;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 560,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-02-02',
  rate_changed_at = NOW()
WHERE no_unit = 3320;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 549,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-02-02',
  rate_changed_at = NOW()
WHERE no_unit = 3321;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 549,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-02-02',
  rate_changed_at = NOW()
WHERE no_unit = 3322;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = 400,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-09-26',
  rate_changed_at = NOW()
WHERE no_unit = 5363;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = 400,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-09-26',
  rate_changed_at = NOW()
WHERE no_unit = 5364;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 549,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-02-02',
  rate_changed_at = NOW()
WHERE no_unit = 3325;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 549,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-02-02',
  rate_changed_at = NOW()
WHERE no_unit = 3326;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = 400,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-09-27',
  rate_changed_at = NOW()
WHERE no_unit = 5366;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = 400,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-09-27',
  rate_changed_at = NOW()
WHERE no_unit = 5367;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = 400,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-09-26',
  rate_changed_at = NOW()
WHERE no_unit = 5368;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 251,
  kontrak_id = 400,
  area_id = NULL,
  harga_sewa_bulanan = 11950000.0,
  on_hire_date = '2024-09-27',
  rate_changed_at = NOW()
WHERE no_unit = 5369;
UPDATE inventory_unit SET
  customer_id = 80,
  customer_location_id = @location_10,
  kontrak_id = 421,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = '2023-02-14',
  rate_changed_at = NOW()
WHERE no_unit = 3331;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 249,
  kontrak_id = 400,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-09-26',
  rate_changed_at = NOW()
WHERE no_unit = 5371;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 971,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2024-09-28',
  rate_changed_at = NOW()
WHERE no_unit = 5372;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 249,
  kontrak_id = 400,
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
  kontrak_id = 437,
  area_id = NULL,
  harga_sewa_bulanan = 10500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5379;
UPDATE inventory_unit SET
  customer_id = 64,
  customer_location_id = 913,
  kontrak_id = 517,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = '2025-04-26',
  rate_changed_at = NOW()
WHERE no_unit = 5380;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 237,
  kontrak_id = 489,
  area_id = NULL,
  harga_sewa_bulanan = 12900000.0,
  on_hire_date = '2022-01-17',
  rate_changed_at = NOW()
WHERE no_unit = 5141;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2025-02-25',
  rate_changed_at = NOW()
WHERE no_unit = 5381;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2025-02-25',
  rate_changed_at = NOW()
WHERE no_unit = 5383;
UPDATE inventory_unit SET
  customer_id = 151,
  customer_location_id = 391,
  kontrak_id = 561,
  area_id = NULL,
  harga_sewa_bulanan = 9500000.0,
  on_hire_date = '2024-10-31',
  rate_changed_at = NOW()
WHERE no_unit = 5384;
UPDATE inventory_unit SET
  customer_id = 97,
  customer_location_id = 972,
  kontrak_id = 368,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-11-28',
  rate_changed_at = NOW()
WHERE no_unit = 5386;
UPDATE inventory_unit SET
  customer_id = 69,
  customer_location_id = 213,
  kontrak_id = 562,
  area_id = NULL,
  harga_sewa_bulanan = 17500000.0,
  on_hire_date = '2024-08-30',
  rate_changed_at = NOW()
WHERE no_unit = 5391;
UPDATE inventory_unit SET
  customer_id = 151,
  customer_location_id = 391,
  kontrak_id = 563,
  area_id = NULL,
  harga_sewa_bulanan = 9500000.0,
  on_hire_date = '2025-01-31',
  rate_changed_at = NOW()
WHERE no_unit = 5549;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = 564,
  area_id = NULL,
  harga_sewa_bulanan = 32500000.0,
  on_hire_date = '2024-10-22',
  rate_changed_at = NOW()
WHERE no_unit = 5393;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = 564,
  area_id = NULL,
  harga_sewa_bulanan = 32500000.0,
  on_hire_date = '2024-10-22',
  rate_changed_at = NOW()
WHERE no_unit = 5394;
UPDATE inventory_unit SET
  customer_id = 30,
  customer_location_id = 98,
  kontrak_id = 565,
  area_id = NULL,
  harga_sewa_bulanan = 9200000.0,
  on_hire_date = '2026-01-05',
  rate_changed_at = NOW()
WHERE no_unit = 5401;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = 468,
  area_id = NULL,
  harga_sewa_bulanan = 62000000.0,
  on_hire_date = '2025-05-21',
  rate_changed_at = NOW()
WHERE no_unit = 5408;
UPDATE inventory_unit SET
  customer_id = 114,
  customer_location_id = 973,
  kontrak_id = 368,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-11-20',
  rate_changed_at = NOW()
WHERE no_unit = 3363;
UPDATE inventory_unit SET
  customer_id = 43,
  customer_location_id = 121,
  kontrak_id = 491,
  area_id = NULL,
  harga_sewa_bulanan = 9900000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3364;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 417,
  area_id = 35,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2025-12-13',
  rate_changed_at = NOW()
WHERE no_unit = 5416;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 251,
  kontrak_id = 400,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2024-12-10',
  rate_changed_at = NOW()
WHERE no_unit = 3369;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 141,
  kontrak_id = 451,
  area_id = NULL,
  harga_sewa_bulanan = 13800000.0,
  on_hire_date = '2023-05-31',
  rate_changed_at = NOW()
WHERE no_unit = 3373;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 911,
  kontrak_id = 379,
  area_id = NULL,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2023-07-04',
  rate_changed_at = NOW()
WHERE no_unit = 3374;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 240,
  kontrak_id = 566,
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
  kontrak_id = 567,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3377;
UPDATE inventory_unit SET
  customer_id = 29,
  customer_location_id = 92,
  kontrak_id = 567,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3378;
UPDATE inventory_unit SET
  customer_id = 29,
  customer_location_id = 92,
  kontrak_id = 567,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3379;
UPDATE inventory_unit SET
  customer_id = 29,
  customer_location_id = 92,
  kontrak_id = 567,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3380;
UPDATE inventory_unit SET
  customer_id = 29,
  customer_location_id = 92,
  kontrak_id = 567,
  area_id = NULL,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3381;
UPDATE inventory_unit SET
  customer_id = 29,
  customer_location_id = 92,
  kontrak_id = 567,
  area_id = NULL,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3382;
UPDATE inventory_unit SET
  customer_id = 50,
  customer_location_id = 947,
  kontrak_id = 485,
  area_id = NULL,
  harga_sewa_bulanan = 24500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3383;
UPDATE inventory_unit SET
  customer_id = 50,
  customer_location_id = 947,
  kontrak_id = 485,
  area_id = NULL,
  harga_sewa_bulanan = 24500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3384;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = 568,
  area_id = NULL,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3385;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 3386;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 3387;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 3388;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 3389;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = 951,
  kontrak_id = 496,
  area_id = NULL,
  harga_sewa_bulanan = 12300000.0,
  on_hire_date = '2023-07-20',
  rate_changed_at = NOW()
WHERE no_unit = 3390;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = 569,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3391;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = 569,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3392;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = 951,
  kontrak_id = 496,
  area_id = NULL,
  harga_sewa_bulanan = 12300000.0,
  on_hire_date = '2023-03-21',
  rate_changed_at = NOW()
WHERE no_unit = 3393;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 419,
  kontrak_id = 402,
  area_id = NULL,
  harga_sewa_bulanan = 23500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1346;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 570,
  area_id = 26,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3394;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 570,
  area_id = 26,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3395;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 570,
  area_id = 26,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3396;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = 448,
  area_id = 26,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3397;
UPDATE inventory_unit SET
  customer_id = 96,
  customer_location_id = 281,
  kontrak_id = 571,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2025-11-13',
  rate_changed_at = NOW()
WHERE no_unit = 3398;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 572,
  area_id = 34,
  harga_sewa_bulanan = 41500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3400;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 573,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 3401;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_6,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2025-09-05',
  rate_changed_at = NOW()
WHERE no_unit = 5441;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_6,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2025-09-05',
  rate_changed_at = NOW()
WHERE no_unit = 5443;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = 951,
  kontrak_id = 496,
  area_id = NULL,
  harga_sewa_bulanan = 12300000.0,
  on_hire_date = '2023-03-21',
  rate_changed_at = NOW()
WHERE no_unit = 3404;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = 951,
  kontrak_id = 496,
  area_id = NULL,
  harga_sewa_bulanan = 12300000.0,
  on_hire_date = '2023-03-21',
  rate_changed_at = NOW()
WHERE no_unit = 3405;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = 270,
  kontrak_id = 574,
  area_id = NULL,
  harga_sewa_bulanan = 22500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5445;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 14900000.0,
  on_hire_date = '2024-11-22',
  rate_changed_at = NOW()
WHERE no_unit = 5449;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = 568,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5562;
UPDATE inventory_unit SET
  customer_id = 161,
  customer_location_id = 974,
  kontrak_id = 575,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3409;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 572,
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
  customer_location_id = 975,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1365;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = 476,
  area_id = NULL,
  harga_sewa_bulanan = 11300000.0,
  on_hire_date = '2023-04-05',
  rate_changed_at = NOW()
WHERE no_unit = 3413;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = 476,
  area_id = NULL,
  harga_sewa_bulanan = 11300000.0,
  on_hire_date = '2023-04-05',
  rate_changed_at = NOW()
WHERE no_unit = 3414;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_12,
  kontrak_id = 460,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = '2023-04-06',
  rate_changed_at = NOW()
WHERE no_unit = 3415;

-- Processed 600/2178 units

UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_12,
  kontrak_id = 576,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = '2023-04-06',
  rate_changed_at = NOW()
WHERE no_unit = 3416;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_12,
  kontrak_id = 460,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = '2023-04-06',
  rate_changed_at = NOW()
WHERE no_unit = 3417;
UPDATE inventory_unit SET
  customer_id = 50,
  customer_location_id = 947,
  kontrak_id = 485,
  area_id = NULL,
  harga_sewa_bulanan = 10700000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3418;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 458,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3419;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = 448,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3420;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 458,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3422;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 458,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3423;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = 400,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-06-14',
  rate_changed_at = NOW()
WHERE no_unit = 5158;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = 448,
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
  customer_location_id = @location_3,
  kontrak_id = 577,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3428;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5469;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5470;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5471;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5472;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5473;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5474;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5475;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5476;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = '2024-12-19',
  rate_changed_at = NOW()
WHERE no_unit = 5477;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 13,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 22030000.0,
  on_hire_date = '2024-05-30',
  rate_changed_at = NOW()
WHERE no_unit = 1390;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 458,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3438;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 458,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3440;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 249,
  kontrak_id = 400,
  area_id = NULL,
  harga_sewa_bulanan = 11950000.0,
  on_hire_date = '2024-06-18',
  rate_changed_at = NOW()
WHERE no_unit = 5161;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 464,
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
  customer_location_id = @location_3,
  kontrak_id = 458,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3444;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 458,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3445;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 578,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3446;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 577,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3447;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = 448,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3449;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = 224,
  kontrak_id = 506,
  area_id = NULL,
  harga_sewa_bulanan = 10100000.0,
  on_hire_date = '2025-02-26',
  rate_changed_at = NOW()
WHERE no_unit = 5162;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 8900000.0,
  on_hire_date = '2025-02-25',
  rate_changed_at = NOW()
WHERE no_unit = 5570;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = 448,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3452;
UPDATE inventory_unit SET
  customer_id = 96,
  customer_location_id = 281,
  kontrak_id = 539,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2024-12-26',
  rate_changed_at = NOW()
WHERE no_unit = 5501;
UPDATE inventory_unit SET
  customer_id = 96,
  customer_location_id = 281,
  kontrak_id = 539,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2024-12-26',
  rate_changed_at = NOW()
WHERE no_unit = 5502;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = 448,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3455;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 8900000.0,
  on_hire_date = '2025-02-25',
  rate_changed_at = NOW()
WHERE no_unit = 5571;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 448,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2024-04-18',
  rate_changed_at = NOW()
WHERE no_unit = 3457;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 390,
  area_id = 26,
  harga_sewa_bulanan = 22500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5509;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 579,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3463;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = 448,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3471;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = 448,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3472;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 458,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3473;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 580,
  area_id = 26,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3474;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 581,
  area_id = 26,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3475;
UPDATE inventory_unit SET
  customer_id = 21,
  customer_location_id = 933,
  kontrak_id = 542,
  area_id = 18,
  harga_sewa_bulanan = 12900000.0,
  on_hire_date = '2023-09-26',
  rate_changed_at = NOW()
WHERE no_unit = 1428;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 580,
  area_id = 26,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3477;
UPDATE inventory_unit SET
  customer_id = 168,
  customer_location_id = 410,
  kontrak_id = 494,
  area_id = NULL,
  harga_sewa_bulanan = 10650000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1430;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 421,
  kontrak_id = 402,
  area_id = NULL,
  harga_sewa_bulanan = 15500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1432;
UPDATE inventory_unit SET
  customer_id = 148,
  customer_location_id = 386,
  kontrak_id = 582,
  area_id = NULL,
  harga_sewa_bulanan = 14250000.0,
  on_hire_date = '2024-01-31',
  rate_changed_at = NOW()
WHERE no_unit = 5528;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = 564,
  area_id = NULL,
  harga_sewa_bulanan = 32500000.0,
  on_hire_date = '2025-01-14',
  rate_changed_at = NOW()
WHERE no_unit = 5529;
UPDATE inventory_unit SET
  customer_id = 29,
  customer_location_id = 92,
  kontrak_id = 567,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3483;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = 564,
  area_id = NULL,
  harga_sewa_bulanan = 32500000.0,
  on_hire_date = '2025-01-14',
  rate_changed_at = NOW()
WHERE no_unit = 5530;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 52,
  kontrak_id = 386,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3485;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 976,
  kontrak_id = 583,
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
  kontrak_id = 584,
  area_id = NULL,
  harga_sewa_bulanan = 16200000.0,
  on_hire_date = '2025-07-29',
  rate_changed_at = NOW()
WHERE no_unit = 3491;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = 224,
  kontrak_id = 506,
  area_id = NULL,
  harga_sewa_bulanan = 10100000.0,
  on_hire_date = '2025-02-26',
  rate_changed_at = NOW()
WHERE no_unit = 5539;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = 448,
  area_id = 26,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3494;
UPDATE inventory_unit SET
  customer_id = 227,
  customer_location_id = 977,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2022-01-24',
  rate_changed_at = NOW()
WHERE no_unit = 1453;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 573,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-26',
  rate_changed_at = NOW()
WHERE no_unit = 3502;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 572,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-25',
  rate_changed_at = NOW()
WHERE no_unit = 3503;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 585,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-25',
  rate_changed_at = NOW()
WHERE no_unit = 3504;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 573,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-26',
  rate_changed_at = NOW()
WHERE no_unit = 3505;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 586,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-25',
  rate_changed_at = NOW()
WHERE no_unit = 3506;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 587,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-25',
  rate_changed_at = NOW()
WHERE no_unit = 3507;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 572,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-25',
  rate_changed_at = NOW()
WHERE no_unit = 3508;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 573,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-26',
  rate_changed_at = NOW()
WHERE no_unit = 3509;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 573,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-26',
  rate_changed_at = NOW()
WHERE no_unit = 3510;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = 949,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1463;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 911,
  kontrak_id = 451,
  area_id = NULL,
  harga_sewa_bulanan = 13800000.0,
  on_hire_date = '2023-06-24',
  rate_changed_at = NOW()
WHERE no_unit = 3511;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 911,
  kontrak_id = 451,
  area_id = NULL,
  harga_sewa_bulanan = 13800000.0,
  on_hire_date = '2023-06-24',
  rate_changed_at = NOW()
WHERE no_unit = 3512;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 911,
  kontrak_id = 451,
  area_id = NULL,
  harga_sewa_bulanan = 13800000.0,
  on_hire_date = '2023-06-24',
  rate_changed_at = NOW()
WHERE no_unit = 3513;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 559,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-26',
  rate_changed_at = NOW()
WHERE no_unit = 3514;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 351,
  kontrak_id = 588,
  area_id = NULL,
  harga_sewa_bulanan = 9250000.0,
  on_hire_date = '2025-11-17',
  rate_changed_at = NOW()
WHERE no_unit = 3515;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 911,
  kontrak_id = 451,
  area_id = NULL,
  harga_sewa_bulanan = 13800000.0,
  on_hire_date = '2023-06-24',
  rate_changed_at = NOW()
WHERE no_unit = 3516;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 41,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 12050000.0,
  on_hire_date = '2023-12-29',
  rate_changed_at = NOW()
WHERE no_unit = 3517;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 573,
  area_id = 34,
  harga_sewa_bulanan = 41500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3518;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 572,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 3519;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 586,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 3520;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 573,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 3521;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 559,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 3522;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 587,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-05-25',
  rate_changed_at = NOW()
WHERE no_unit = 3523;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 586,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 3524;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 589,
  area_id = 34,
  harga_sewa_bulanan = 20564000.0,
  on_hire_date = '2023-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 3526;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 559,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 3527;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 572,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 3528;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 573,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-05-26',
  rate_changed_at = NOW()
WHERE no_unit = 3529;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 587,
  area_id = 34,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-05-26',
  rate_changed_at = NOW()
WHERE no_unit = 3530;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 589,
  area_id = 34,
  harga_sewa_bulanan = 42400000.0,
  on_hire_date = '2023-05-26',
  rate_changed_at = NOW()
WHERE no_unit = 3531;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 587,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-25',
  rate_changed_at = NOW()
WHERE no_unit = 3532;
UPDATE inventory_unit SET
  customer_id = 140,
  customer_location_id = 360,
  kontrak_id = 481,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2023-06-15',
  rate_changed_at = NOW()
WHERE no_unit = 3533;
UPDATE inventory_unit SET
  customer_id = 177,
  customer_location_id = 422,
  kontrak_id = 376,
  area_id = 21,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3534;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = 949,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3535;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 346,
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2025-02-25',
  rate_changed_at = NOW()
WHERE no_unit = 5579;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_7,
  kontrak_id = 590,
  area_id = 19,
  harga_sewa_bulanan = 10450000.0,
  on_hire_date = '2023-09-14',
  rate_changed_at = NOW()
WHERE no_unit = 3537;
UPDATE inventory_unit SET
  customer_id = 140,
  customer_location_id = 360,
  kontrak_id = 481,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2023-06-08',
  rate_changed_at = NOW()
WHERE no_unit = 3538;

-- Processed 700/2178 units

UPDATE inventory_unit SET
  customer_id = 140,
  customer_location_id = 360,
  kontrak_id = 481,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2023-06-08',
  rate_changed_at = NOW()
WHERE no_unit = 3539;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 350,
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2025-02-27',
  rate_changed_at = NOW()
WHERE no_unit = 5580;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_19,
  kontrak_id = 536,
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
  customer_location_id = 951,
  kontrak_id = 496,
  area_id = NULL,
  harga_sewa_bulanan = 13800000.0,
  on_hire_date = '2023-07-20',
  rate_changed_at = NOW()
WHERE no_unit = 3543;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 587,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-25',
  rate_changed_at = NOW()
WHERE no_unit = 3544;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 559,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-25',
  rate_changed_at = NOW()
WHERE no_unit = 3545;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 587,
  area_id = 34,
  harga_sewa_bulanan = 34100000.0,
  on_hire_date = '2023-05-25',
  rate_changed_at = NOW()
WHERE no_unit = 3546;
UPDATE inventory_unit SET
  customer_id = 43,
  customer_location_id = 121,
  kontrak_id = 491,
  area_id = NULL,
  harga_sewa_bulanan = 10550000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3547;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 33,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 12050000.0,
  on_hire_date = '2023-12-28',
  rate_changed_at = NOW()
WHERE no_unit = 3548;
UPDATE inventory_unit SET
  customer_id = 99,
  customer_location_id = 978,
  kontrak_id = 543,
  area_id = NULL,
  harga_sewa_bulanan = 9250000.0,
  on_hire_date = '2024-09-07',
  rate_changed_at = NOW()
WHERE no_unit = 1501;
UPDATE inventory_unit SET
  customer_id = 188,
  customer_location_id = 433,
  kontrak_id = 591,
  area_id = 13,
  harga_sewa_bulanan = 25000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5589;
UPDATE inventory_unit SET
  customer_id = 30,
  customer_location_id = 95,
  kontrak_id = 592,
  area_id = NULL,
  harga_sewa_bulanan = 10710000.0,
  on_hire_date = '2025-10-31',
  rate_changed_at = NOW()
WHERE no_unit = 3551;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = 593,
  area_id = 27,
  harga_sewa_bulanan = 24000000.0,
  on_hire_date = '2023-07-20',
  rate_changed_at = NOW()
WHERE no_unit = 3552;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = 223,
  kontrak_id = 594,
  area_id = NULL,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = '2025-05-09',
  rate_changed_at = NOW()
WHERE no_unit = 5599;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 23,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 11850000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 3554;
UPDATE inventory_unit SET
  customer_id = 142,
  customer_location_id = 979,
  kontrak_id = @contract_4,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = '2025-08-01',
  rate_changed_at = NOW()
WHERE no_unit = 5602;
UPDATE inventory_unit SET
  customer_id = 79,
  customer_location_id = 228,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 30500000.0,
  on_hire_date = '2025-05-07',
  rate_changed_at = NOW()
WHERE no_unit = 5604;
UPDATE inventory_unit SET
  customer_id = 183,
  customer_location_id = 428,
  kontrak_id = 596,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3557;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 123,
  kontrak_id = 370,
  area_id = 17,
  harga_sewa_bulanan = 7700.0,
  on_hire_date = '2025-07-31',
  rate_changed_at = NOW()
WHERE no_unit = 3558;
UPDATE inventory_unit SET
  customer_id = 79,
  customer_location_id = 228,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 30500000.0,
  on_hire_date = '2025-05-07',
  rate_changed_at = NOW()
WHERE no_unit = 5606;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 52,
  kontrak_id = 386,
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
  kontrak_id = 597,
  area_id = NULL,
  harga_sewa_bulanan = 11800000.0,
  on_hire_date = '2024-03-14',
  rate_changed_at = NOW()
WHERE no_unit = 3561;
UPDATE inventory_unit SET
  customer_id = 96,
  customer_location_id = 281,
  kontrak_id = 571,
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
  kontrak_id = 597,
  area_id = NULL,
  harga_sewa_bulanan = 11800000.0,
  on_hire_date = '2024-03-14',
  rate_changed_at = NOW()
WHERE no_unit = 3563;
UPDATE inventory_unit SET
  customer_id = 138,
  customer_location_id = 357,
  kontrak_id = 598,
  area_id = 19,
  harga_sewa_bulanan = 16000000.0,
  on_hire_date = '2024-03-20',
  rate_changed_at = NOW()
WHERE no_unit = 3564;
UPDATE inventory_unit SET
  customer_id = 7,
  customer_location_id = @location_11,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 27000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3565;
UPDATE inventory_unit SET
  customer_id = 140,
  customer_location_id = 360,
  kontrak_id = 481,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = '2024-10-07',
  rate_changed_at = NOW()
WHERE no_unit = 3566;
UPDATE inventory_unit SET
  customer_id = 140,
  customer_location_id = 360,
  kontrak_id = 481,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2022-06-01',
  rate_changed_at = NOW()
WHERE no_unit = 3568;
UPDATE inventory_unit SET
  customer_id = 225,
  customer_location_id = 980,
  kontrak_id = NULL,
  area_id = 21,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3570;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_19,
  kontrak_id = 536,
  area_id = 19,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3571;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_19,
  kontrak_id = 536,
  area_id = 19,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3572;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_19,
  kontrak_id = 536,
  area_id = 19,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3573;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_19,
  kontrak_id = 536,
  area_id = 19,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3574;
UPDATE inventory_unit SET
  customer_id = 64,
  customer_location_id = 913,
  kontrak_id = 517,
  area_id = NULL,
  harga_sewa_bulanan = 27000000.0,
  on_hire_date = '2025-04-28',
  rate_changed_at = NOW()
WHERE no_unit = 5614;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_19,
  kontrak_id = 536,
  area_id = 19,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3576;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = 432,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = '2023-11-22',
  rate_changed_at = NOW()
WHERE no_unit = 3577;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = 432,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = '2023-11-23',
  rate_changed_at = NOW()
WHERE no_unit = 3578;
UPDATE inventory_unit SET
  customer_id = 64,
  customer_location_id = 913,
  kontrak_id = 517,
  area_id = NULL,
  harga_sewa_bulanan = 11200000.0,
  on_hire_date = '2025-04-26',
  rate_changed_at = NOW()
WHERE no_unit = 5619;
UPDATE inventory_unit SET
  customer_id = 53,
  customer_location_id = @location_22,
  kontrak_id = 599,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-05-02',
  rate_changed_at = NOW()
WHERE no_unit = 5620;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 251,
  kontrak_id = 400,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2024-06-07',
  rate_changed_at = NOW()
WHERE no_unit = 3581;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 6900000.0,
  on_hire_date = '2025-02-24',
  rate_changed_at = NOW()
WHERE no_unit = 3582;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = 600,
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
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 12050000.0,
  on_hire_date = '2023-12-18',
  rate_changed_at = NOW()
WHERE no_unit = 3588;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 40,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 12050000.0,
  on_hire_date = '2023-12-29',
  rate_changed_at = NOW()
WHERE no_unit = 3589;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 23,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 11850000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 3590;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 21,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 11850000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 3591;
UPDATE inventory_unit SET
  customer_id = 127,
  customer_location_id = 338,
  kontrak_id = 601,
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
  customer_location_id = @location_15,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 32500000.0,
  on_hire_date = '2025-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 5635;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = 564,
  area_id = NULL,
  harga_sewa_bulanan = 32500000.0,
  on_hire_date = '2025-04-30',
  rate_changed_at = NOW()
WHERE no_unit = 5636;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 174,
  kontrak_id = 564,
  area_id = NULL,
  harga_sewa_bulanan = 32500000.0,
  on_hire_date = '2025-04-30',
  rate_changed_at = NOW()
WHERE no_unit = 5637;
UPDATE inventory_unit SET
  customer_id = 79,
  customer_location_id = 228,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2025-06-12',
  rate_changed_at = NOW()
WHERE no_unit = 5643;
UPDATE inventory_unit SET
  customer_id = 37,
  customer_location_id = 110,
  kontrak_id = 602,
  area_id = 19,
  harga_sewa_bulanan = 9100000.0,
  on_hire_date = '2021-06-28',
  rate_changed_at = NOW()
WHERE no_unit = 3598;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_7,
  kontrak_id = 590,
  area_id = 19,
  harga_sewa_bulanan = 9450000.0,
  on_hire_date = '2023-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 3605;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_7,
  kontrak_id = 590,
  area_id = 19,
  harga_sewa_bulanan = 10450000.0,
  on_hire_date = '2023-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 3606;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_7,
  kontrak_id = 590,
  area_id = 19,
  harga_sewa_bulanan = 10450000.0,
  on_hire_date = '2023-09-30',
  rate_changed_at = NOW()
WHERE no_unit = 3607;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 6900000.0,
  on_hire_date = '2025-02-21',
  rate_changed_at = NOW()
WHERE no_unit = 3609;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 6900000.0,
  on_hire_date = '2025-02-21',
  rate_changed_at = NOW()
WHERE no_unit = 3610;
UPDATE inventory_unit SET
  customer_id = 27,
  customer_location_id = 89,
  kontrak_id = 603,
  area_id = 19,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = '2025-06-25',
  rate_changed_at = NOW()
WHERE no_unit = 5657;
UPDATE inventory_unit SET
  customer_id = 177,
  customer_location_id = 422,
  kontrak_id = 376,
  area_id = 21,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3612;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 151,
  kontrak_id = 604,
  area_id = NULL,
  harga_sewa_bulanan = 330000.0,
  on_hire_date = '2024-05-06',
  rate_changed_at = NOW()
WHERE no_unit = 3613;
UPDATE inventory_unit SET
  customer_id = 7,
  customer_location_id = @location_11,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 50000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3615;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_15,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 32500000.0,
  on_hire_date = '2025-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 5663;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_7,
  kontrak_id = 590,
  area_id = 19,
  harga_sewa_bulanan = 9450000.0,
  on_hire_date = '2023-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 3617;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_7,
  kontrak_id = 590,
  area_id = 19,
  harga_sewa_bulanan = 9450000.0,
  on_hire_date = '2023-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 3618;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_7,
  kontrak_id = 590,
  area_id = 19,
  harga_sewa_bulanan = 9450000.0,
  on_hire_date = '2023-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 3619;
UPDATE inventory_unit SET
  customer_id = 121,
  customer_location_id = 328,
  kontrak_id = 467,
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
  customer_location_id = @location_4,
  kontrak_id = 391,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2025-03-19',
  rate_changed_at = NOW()
WHERE no_unit = 3622;
UPDATE inventory_unit SET
  customer_id = 183,
  customer_location_id = 428,
  kontrak_id = 605,
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
  kontrak_id = 606,
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
  kontrak_id = 607,
  area_id = NULL,
  harga_sewa_bulanan = 10450000.0,
  on_hire_date = '2025-01-07',
  rate_changed_at = NOW()
WHERE no_unit = 3628;
UPDATE inventory_unit SET
  customer_id = 34,
  customer_location_id = 107,
  kontrak_id = 608,
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
  kontrak_id = 437,
  area_id = NULL,
  harga_sewa_bulanan = 10500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5676;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = 448,
  area_id = 26,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 5677;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 6900000.0,
  on_hire_date = '2025-02-24',
  rate_changed_at = NOW()
WHERE no_unit = 3633;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 251,
  kontrak_id = 400,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2024-09-27',
  rate_changed_at = NOW()
WHERE no_unit = 3634;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = 448,
  area_id = 26,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 5678;
UPDATE inventory_unit SET
  customer_id = 140,
  customer_location_id = 360,
  kontrak_id = 481,
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
  customer_location_id = 982,
  kontrak_id = 609,
  area_id = NULL,
  harga_sewa_bulanan = 50200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5682;
UPDATE inventory_unit SET
  customer_id = 79,
  customer_location_id = 228,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 30500000.0,
  on_hire_date = '2025-05-07',
  rate_changed_at = NOW()
WHERE no_unit = 5608;
UPDATE inventory_unit SET
  customer_id = 48,
  customer_location_id = 939,
  kontrak_id = 466,
  area_id = NULL,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5684;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_15,
  kontrak_id = 484,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = '2023-08-25',
  rate_changed_at = NOW()
WHERE no_unit = 3645;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_15,
  kontrak_id = 484,
  area_id = NULL,
  harga_sewa_bulanan = 9700000.0,
  on_hire_date = '2023-08-25',
  rate_changed_at = NOW()
WHERE no_unit = 3646;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = 225,
  kontrak_id = 610,
  area_id = NULL,
  harga_sewa_bulanan = 12400000.0,
  on_hire_date = '2025-05-08',
  rate_changed_at = NOW()
WHERE no_unit = 5609;
UPDATE inventory_unit SET
  customer_id = 40,
  customer_location_id = 983,
  kontrak_id = 611,
  area_id = NULL,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = '2024-07-31',
  rate_changed_at = NOW()
WHERE no_unit = 1600;
UPDATE inventory_unit SET
  customer_id = 171,
  customer_location_id = 413,
  kontrak_id = 612,
  area_id = NULL,
  harga_sewa_bulanan = 13500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1601;

-- Processed 800/2178 units

UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = 613,
  area_id = NULL,
  harga_sewa_bulanan = 6950000.0,
  on_hire_date = '2024-12-23',
  rate_changed_at = NOW()
WHERE no_unit = 3650;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = 225,
  kontrak_id = 610,
  area_id = NULL,
  harga_sewa_bulanan = 12400000.0,
  on_hire_date = '2025-05-08',
  rate_changed_at = NOW()
WHERE no_unit = 5610;
UPDATE inventory_unit SET
  customer_id = 226,
  customer_location_id = 982,
  kontrak_id = 609,
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
  kontrak_id = 614,
  area_id = NULL,
  harga_sewa_bulanan = 41500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3653;
UPDATE inventory_unit SET
  customer_id = 177,
  customer_location_id = 422,
  kontrak_id = 376,
  area_id = 21,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3654;
UPDATE inventory_unit SET
  customer_id = 64,
  customer_location_id = 913,
  kontrak_id = 383,
  area_id = NULL,
  harga_sewa_bulanan = 10800000.0,
  on_hire_date = '2024-08-27',
  rate_changed_at = NOW()
WHERE no_unit = 1608;
UPDATE inventory_unit SET
  customer_id = 12,
  customer_location_id = 65,
  kontrak_id = 615,
  area_id = NULL,
  harga_sewa_bulanan = 31000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3657;
UPDATE inventory_unit SET
  customer_id = 12,
  customer_location_id = 65,
  kontrak_id = 616,
  area_id = NULL,
  harga_sewa_bulanan = 31000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3658;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = 223,
  kontrak_id = 610,
  area_id = NULL,
  harga_sewa_bulanan = 12400000.0,
  on_hire_date = '2025-05-09',
  rate_changed_at = NOW()
WHERE no_unit = 5611;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = 497,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3660;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = @location_19,
  kontrak_id = 536,
  area_id = 19,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3661;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = 223,
  kontrak_id = 610,
  area_id = NULL,
  harga_sewa_bulanan = 12400000.0,
  on_hire_date = '2025-05-09',
  rate_changed_at = NOW()
WHERE no_unit = 5612;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = 984,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-09-05',
  rate_changed_at = NOW()
WHERE no_unit = 3663;
UPDATE inventory_unit SET
  customer_id = 193,
  customer_location_id = 438,
  kontrak_id = 617,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5703;
UPDATE inventory_unit SET
  customer_id = 13,
  customer_location_id = 985,
  kontrak_id = 618,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3665;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 6900000.0,
  on_hire_date = '2025-02-24',
  rate_changed_at = NOW()
WHERE no_unit = 3666;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = 619,
  area_id = NULL,
  harga_sewa_bulanan = 6950000.0,
  on_hire_date = '2024-12-23',
  rate_changed_at = NOW()
WHERE no_unit = 3667;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = 223,
  kontrak_id = 610,
  area_id = NULL,
  harga_sewa_bulanan = 12400000.0,
  on_hire_date = '2025-05-09',
  rate_changed_at = NOW()
WHERE no_unit = 5613;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 7100000.0,
  on_hire_date = '2025-02-24',
  rate_changed_at = NOW()
WHERE no_unit = 3669;
UPDATE inventory_unit SET
  customer_id = 140,
  customer_location_id = 360,
  kontrak_id = 620,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = '2025-12-10',
  rate_changed_at = NOW()
WHERE no_unit = 5713;
UPDATE inventory_unit SET
  customer_id = 188,
  customer_location_id = 433,
  kontrak_id = 621,
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
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 6900000.0,
  on_hire_date = '2025-02-24',
  rate_changed_at = NOW()
WHERE no_unit = 3673;
UPDATE inventory_unit SET
  customer_id = 122,
  customer_location_id = 329,
  kontrak_id = 622,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = '2025-11-17',
  rate_changed_at = NOW()
WHERE no_unit = 5719;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_5,
  kontrak_id = 623,
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
  customer_location_id = 911,
  kontrak_id = 451,
  area_id = NULL,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = '2023-07-24',
  rate_changed_at = NOW()
WHERE no_unit = 3679;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 911,
  kontrak_id = 451,
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
  customer_location_id = 986,
  kontrak_id = 624,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2024-05-16',
  rate_changed_at = NOW()
WHERE no_unit = 1634;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 911,
  kontrak_id = 451,
  area_id = NULL,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = '2023-07-25',
  rate_changed_at = NOW()
WHERE no_unit = 3681;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 911,
  kontrak_id = 451,
  area_id = NULL,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = '2023-07-25',
  rate_changed_at = NOW()
WHERE no_unit = 3682;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 911,
  kontrak_id = 451,
  area_id = NULL,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = '2023-07-26',
  rate_changed_at = NOW()
WHERE no_unit = 3683;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 911,
  kontrak_id = 451,
  area_id = NULL,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = '2023-07-26',
  rate_changed_at = NOW()
WHERE no_unit = 3684;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 6900000.0,
  on_hire_date = '2025-02-24',
  rate_changed_at = NOW()
WHERE no_unit = 3686;
UPDATE inventory_unit SET
  customer_id = 226,
  customer_location_id = 982,
  kontrak_id = 609,
  area_id = NULL,
  harga_sewa_bulanan = 10500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3687;
UPDATE inventory_unit SET
  customer_id = 72,
  customer_location_id = 218,
  kontrak_id = 492,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3688;
UPDATE inventory_unit SET
  customer_id = 64,
  customer_location_id = 913,
  kontrak_id = 517,
  area_id = NULL,
  harga_sewa_bulanan = 11200000.0,
  on_hire_date = '2025-04-26',
  rate_changed_at = NOW()
WHERE no_unit = 5617;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 971,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2024-09-27',
  rate_changed_at = NOW()
WHERE no_unit = 3691;
UPDATE inventory_unit SET
  customer_id = 98,
  customer_location_id = 285,
  kontrak_id = 625,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2023-10-02',
  rate_changed_at = NOW()
WHERE no_unit = 3692;
UPDATE inventory_unit SET
  customer_id = 88,
  customer_location_id = 987,
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
  customer_location_id = 909,
  kontrak_id = 626,
  area_id = 33,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = '2025-08-02',
  rate_changed_at = NOW()
WHERE no_unit = 5738;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_21,
  kontrak_id = 552,
  area_id = 33,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3696;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = 271,
  kontrak_id = 627,
  area_id = 18,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3697;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = 271,
  kontrak_id = 628,
  area_id = 18,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3698;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_5,
  kontrak_id = 629,
  area_id = 19,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3699;
UPDATE inventory_unit SET
  customer_id = 86,
  customer_location_id = @location_1,
  kontrak_id = 375,
  area_id = NULL,
  harga_sewa_bulanan = 5500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1652;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_5,
  kontrak_id = 630,
  area_id = 19,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3700;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_5,
  kontrak_id = 631,
  area_id = 19,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3701;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = 271,
  kontrak_id = 632,
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
  customer_location_id = 911,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-12-03',
  rate_changed_at = NOW()
WHERE no_unit = 3704;
UPDATE inventory_unit SET
  customer_id = 30,
  customer_location_id = 97,
  kontrak_id = 633,
  area_id = NULL,
  harga_sewa_bulanan = 10710000.0,
  on_hire_date = '2025-10-11',
  rate_changed_at = NOW()
WHERE no_unit = 3706;
UPDATE inventory_unit SET
  customer_id = 170,
  customer_location_id = 412,
  kontrak_id = 634,
  area_id = NULL,
  harga_sewa_bulanan = 22000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1659;
UPDATE inventory_unit SET
  customer_id = 170,
  customer_location_id = 412,
  kontrak_id = 634,
  area_id = NULL,
  harga_sewa_bulanan = 22000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1660;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = 400,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-06-18',
  rate_changed_at = NOW()
WHERE no_unit = 3707;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = 271,
  kontrak_id = 635,
  area_id = 18,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3708;
UPDATE inventory_unit SET
  customer_id = 96,
  customer_location_id = 281,
  kontrak_id = 571,
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
  kontrak_id = 635,
  area_id = 18,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3711;
UPDATE inventory_unit SET
  customer_id = 96,
  customer_location_id = 281,
  kontrak_id = 571,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2025-11-13',
  rate_changed_at = NOW()
WHERE no_unit = 3713;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 250,
  kontrak_id = 400,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2023-12-22',
  rate_changed_at = NOW()
WHERE no_unit = 3715;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = 600,
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
  customer_location_id = @location_6,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2025-08-28',
  rate_changed_at = NOW()
WHERE no_unit = 5758;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = 636,
  area_id = NULL,
  harga_sewa_bulanan = 10500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5759;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = 637,
  area_id = 18,
  harga_sewa_bulanan = 5750000.0,
  on_hire_date = '2022-03-17',
  rate_changed_at = NOW()
WHERE no_unit = 5624;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_6,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-08-29',
  rate_changed_at = NOW()
WHERE no_unit = 5760;
UPDATE inventory_unit SET
  customer_id = 86,
  customer_location_id = @location_1,
  kontrak_id = 375,
  area_id = NULL,
  harga_sewa_bulanan = 5500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1674;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 112,
  kontrak_id = 638,
  area_id = NULL,
  harga_sewa_bulanan = 10050000.0,
  on_hire_date = '2025-08-21',
  rate_changed_at = NOW()
WHERE no_unit = 5763;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 113,
  kontrak_id = 639,
  area_id = NULL,
  harga_sewa_bulanan = 10050000.0,
  on_hire_date = '2025-08-26',
  rate_changed_at = NOW()
WHERE no_unit = 5764;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = 637,
  area_id = 18,
  harga_sewa_bulanan = 5750000.0,
  on_hire_date = '2022-03-17',
  rate_changed_at = NOW()
WHERE no_unit = 5625;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 114,
  kontrak_id = 640,
  area_id = NULL,
  harga_sewa_bulanan = 10050000.0,
  on_hire_date = '2025-08-21',
  rate_changed_at = NOW()
WHERE no_unit = 5765;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 113,
  kontrak_id = 641,
  area_id = NULL,
  harga_sewa_bulanan = 10050000.0,
  on_hire_date = '2025-08-21',
  rate_changed_at = NOW()
WHERE no_unit = 5766;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 112,
  kontrak_id = 642,
  area_id = NULL,
  harga_sewa_bulanan = 10050000.0,
  on_hire_date = '2025-08-26',
  rate_changed_at = NOW()
WHERE no_unit = 5767;
UPDATE inventory_unit SET
  customer_id = 5,
  customer_location_id = 52,
  kontrak_id = 386,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3729;
UPDATE inventory_unit SET
  customer_id = 118,
  customer_location_id = 322,
  kontrak_id = 368,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2021-08-21',
  rate_changed_at = NOW()
WHERE no_unit = 3730;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 911,
  kontrak_id = 643,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2023-09-25',
  rate_changed_at = NOW()
WHERE no_unit = 3731;
UPDATE inventory_unit SET
  customer_id = 129,
  customer_location_id = 342,
  kontrak_id = 368,
  area_id = 19,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2022-05-13',
  rate_changed_at = NOW()
WHERE no_unit = 3732;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 152,
  kontrak_id = 379,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2025-11-17',
  rate_changed_at = NOW()
WHERE no_unit = 3733;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = 644,
  area_id = NULL,
  harga_sewa_bulanan = 6950000.0,
  on_hire_date = '2024-12-23',
  rate_changed_at = NOW()
WHERE no_unit = 3734;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = 600,
  area_id = 18,
  harga_sewa_bulanan = 5750000.0,
  on_hire_date = '2024-04-29',
  rate_changed_at = NOW()
WHERE no_unit = 5626;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = 600,
  area_id = 18,
  harga_sewa_bulanan = 5750000.0,
  on_hire_date = '2024-03-19',
  rate_changed_at = NOW()
WHERE no_unit = 5627;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = 232,
  kontrak_id = 645,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2024-05-29',
  rate_changed_at = NOW()
WHERE no_unit = 3737;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 114,
  kontrak_id = 646,
  area_id = NULL,
  harga_sewa_bulanan = 10050000.0,
  on_hire_date = '2025-08-26',
  rate_changed_at = NOW()
WHERE no_unit = 5779;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 114,
  kontrak_id = 647,
  area_id = NULL,
  harga_sewa_bulanan = 10050000.0,
  on_hire_date = '2025-08-26',
  rate_changed_at = NOW()
WHERE no_unit = 5780;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = 648,
  area_id = 18,
  harga_sewa_bulanan = 5750000.0,
  on_hire_date = '2022-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 5628;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = 232,
  kontrak_id = 645,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = '2025-11-05',
  rate_changed_at = NOW()
WHERE no_unit = 3741;
UPDATE inventory_unit SET
  customer_id = 148,
  customer_location_id = 388,
  kontrak_id = 502,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = '2025-08-20',
  rate_changed_at = NOW()
WHERE no_unit = 5781;
UPDATE inventory_unit SET
  customer_id = 122,
  customer_location_id = 329,
  kontrak_id = 649,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = '2025-11-26',
  rate_changed_at = NOW()
WHERE no_unit = 5782;
UPDATE inventory_unit SET
  customer_id = 108,
  customer_location_id = 311,
  kontrak_id = 368,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2025-08-27',
  rate_changed_at = NOW()
WHERE no_unit = 5786;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 247,
  kontrak_id = 432,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = '2023-11-22',
  rate_changed_at = NOW()
WHERE no_unit = 3745;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = 650,
  area_id = 18,
  harga_sewa_bulanan = 5750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5629;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-09-15',
  rate_changed_at = NOW()
WHERE no_unit = 5791;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-09-15',
  rate_changed_at = NOW()
WHERE no_unit = 5792;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_6,
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
  kontrak_id = 651,
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
  customer_location_id = @location_5,
  kontrak_id = 652,
  area_id = 19,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5798;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 5000000.0,
  on_hire_date = '2025-09-15',
  rate_changed_at = NOW()
WHERE no_unit = 5800;
UPDATE inventory_unit SET
  customer_id = 118,
  customer_location_id = 322,
  kontrak_id = 368,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-12-05',
  rate_changed_at = NOW()
WHERE no_unit = 3754;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = 653,
  area_id = 18,
  harga_sewa_bulanan = 5750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5631;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 5000000.0,
  on_hire_date = '2025-09-15',
  rate_changed_at = NOW()
WHERE no_unit = 5801;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 5000000.0,
  on_hire_date = '2025-09-15',
  rate_changed_at = NOW()
WHERE no_unit = 5803;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = 988,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = '2025-03-20',
  rate_changed_at = NOW()
WHERE no_unit = 3758;
UPDATE inventory_unit SET
  customer_id = 108,
  customer_location_id = 311,
  kontrak_id = 368,
  area_id = NULL,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = '2025-09-02',
  rate_changed_at = NOW()
WHERE no_unit = 5804;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-09-15',
  rate_changed_at = NOW()
WHERE no_unit = 5805;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = @location_7,
  kontrak_id = 654,
  area_id = 19,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2023-10-19',
  rate_changed_at = NOW()
WHERE no_unit = 3761;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = 655,
  area_id = NULL,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5806;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = 989,
  kontrak_id = 656,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-12-02',
  rate_changed_at = NOW()
WHERE no_unit = 3763;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 990,
  kontrak_id = 657,
  area_id = 33,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-12-02',
  rate_changed_at = NOW()
WHERE no_unit = 3764;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = @location_23,
  kontrak_id = 658,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-12-02',
  rate_changed_at = NOW()
WHERE no_unit = 3765;
UPDATE inventory_unit SET
  customer_id = 74,
  customer_location_id = 992,
  kontrak_id = 659,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-11-16',
  rate_changed_at = NOW()
WHERE no_unit = 3766;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 993,
  kontrak_id = 660,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-12-05',
  rate_changed_at = NOW()
WHERE no_unit = 3767;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 994,
  kontrak_id = 661,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-12-06',
  rate_changed_at = NOW()
WHERE no_unit = 3768;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = @location_24,
  kontrak_id = 662,
  area_id = 13,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-12-08',
  rate_changed_at = NOW()
WHERE no_unit = 3769;
UPDATE inventory_unit SET
  customer_id = 30,
  customer_location_id = 96,
  kontrak_id = 663,
  area_id = NULL,
  harga_sewa_bulanan = 16524000.0,
  on_hire_date = '2025-09-03',
  rate_changed_at = NOW()
WHERE no_unit = 5809;
UPDATE inventory_unit SET
  customer_id = 74,
  customer_location_id = 996,
  kontrak_id = 664,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-11-16',
  rate_changed_at = NOW()
WHERE no_unit = 3771;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 997,
  kontrak_id = 665,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-12-06',
  rate_changed_at = NOW()
WHERE no_unit = 3772;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = 423,
  area_id = NULL,
  harga_sewa_bulanan = 19500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5812;
UPDATE inventory_unit SET
  customer_id = 7,
  customer_location_id = @location_11,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5813;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_6,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-09-26',
  rate_changed_at = NOW()
WHERE no_unit = 5814;
UPDATE inventory_unit SET
  customer_id = 146,
  customer_location_id = 384,
  kontrak_id = 666,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = '2025-10-10',
  rate_changed_at = NOW()
WHERE no_unit = 5820;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 7100000.0,
  on_hire_date = '2025-02-24',
  rate_changed_at = NOW()
WHERE no_unit = 3777;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 5000000.0,
  on_hire_date = '2025-09-15',
  rate_changed_at = NOW()
WHERE no_unit = 5822;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-09-15',
  rate_changed_at = NOW()
WHERE no_unit = 5823;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = 448,
  area_id = 26,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3783;
UPDATE inventory_unit SET
  customer_id = 164,
  customer_location_id = 406,
  kontrak_id = 667,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1736;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = 448,
  area_id = 26,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3784;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = 448,
  area_id = 26,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3785;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = 448,
  area_id = 26,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3786;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = '2025-10-01',
  rate_changed_at = NOW()
WHERE no_unit = 3787;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = 668,
  area_id = NULL,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3788;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
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
  kontrak_id = 448,
  area_id = 26,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3792;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 998,
  kontrak_id = 669,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-12-06',
  rate_changed_at = NOW()
WHERE no_unit = 3793;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 999,
  kontrak_id = 670,
  area_id = 2,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-12-12',
  rate_changed_at = NOW()
WHERE no_unit = 3794;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 1000,
  kontrak_id = 671,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-12-12',
  rate_changed_at = NOW()
WHERE no_unit = 3795;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = @location_25,
  kontrak_id = 672,
  area_id = 16,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-12-13',
  rate_changed_at = NOW()
WHERE no_unit = 3796;
UPDATE inventory_unit SET
  customer_id = 42,
  customer_location_id = @location_26,
  kontrak_id = 673,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = '2025-11-06',
  rate_changed_at = NOW()
WHERE no_unit = 3798;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 21,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-12-22',
  rate_changed_at = NOW()
WHERE no_unit = 3801;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 27,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 10800000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 3802;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 34,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-12-28',
  rate_changed_at = NOW()
WHERE no_unit = 3803;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 32,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-12-28',
  rate_changed_at = NOW()
WHERE no_unit = 3804;
UPDATE inventory_unit SET
  customer_id = 102,
  customer_location_id = 290,
  kontrak_id = 493,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1760;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = 448,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2024-04-29',
  rate_changed_at = NOW()
WHERE no_unit = 3811;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = 448,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2024-04-29',
  rate_changed_at = NOW()
WHERE no_unit = 3812;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = 448,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2024-04-29',
  rate_changed_at = NOW()
WHERE no_unit = 3815;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 578,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3817;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 578,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3818;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 578,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3819;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 578,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3820;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 149,
  kontrak_id = 643,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2023-09-25',
  rate_changed_at = NOW()
WHERE no_unit = 1773;
UPDATE inventory_unit SET
  customer_id = 146,
  customer_location_id = 384,
  kontrak_id = 674,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = '2024-02-02',
  rate_changed_at = NOW()
WHERE no_unit = 3822;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 578,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3823;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = 675,
  area_id = NULL,
  harga_sewa_bulanan = 6800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5865;
UPDATE inventory_unit SET
  customer_id = 62,
  customer_location_id = 1003,
  kontrak_id = 676,
  area_id = NULL,
  harga_sewa_bulanan = 37000000.0,
  on_hire_date = '2022-07-18',
  rate_changed_at = NOW()
WHERE no_unit = 1777;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 578,
  area_id = 26,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3825;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = 675,
  area_id = NULL,
  harga_sewa_bulanan = 6800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5866;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_3,
  kontrak_id = 578,
  area_id = 26,
  harga_sewa_bulanan = 3170000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3828;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 5200000.0,
  on_hire_date = '2025-02-20',
  rate_changed_at = NOW()
WHERE no_unit = 3830;
UPDATE inventory_unit SET
  customer_id = 225,
  customer_location_id = 980,
  kontrak_id = NULL,
  area_id = 21,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1783;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 246,
  kontrak_id = 432,
  area_id = NULL,
  harga_sewa_bulanan = 8200000.0,
  on_hire_date = '2023-11-24',
  rate_changed_at = NOW()
WHERE no_unit = 3832;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 245,
  kontrak_id = 432,
  area_id = NULL,
  harga_sewa_bulanan = 8200000.0,
  on_hire_date = '2023-11-24',
  rate_changed_at = NOW()
WHERE no_unit = 3833;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 247,
  kontrak_id = 432,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2023-11-24',
  rate_changed_at = NOW()
WHERE no_unit = 3834;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 249,
  kontrak_id = 432,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2023-11-23',
  rate_changed_at = NOW()
WHERE no_unit = 3835;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 249,
  kontrak_id = 432,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2023-11-24',
  rate_changed_at = NOW()
WHERE no_unit = 3836;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = 675,
  area_id = NULL,
  harga_sewa_bulanan = 6800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5882;
UPDATE inventory_unit SET
  customer_id = 90,
  customer_location_id = @location_13,
  kontrak_id = 470,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3838;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 248,
  kontrak_id = 432,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = '2023-11-22',
  rate_changed_at = NOW()
WHERE no_unit = 3841;
UPDATE inventory_unit SET
  customer_id = 84,
  customer_location_id = 1004,
  kontrak_id = 368,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2024-01-11',
  rate_changed_at = NOW()
WHERE no_unit = 3843;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_2,
  kontrak_id = 380,
  area_id = 35,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5893;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 251,
  kontrak_id = 432,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = '2023-11-23',
  rate_changed_at = NOW()
WHERE no_unit = 3846;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = @location_6,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2025-10-03',
  rate_changed_at = NOW()
WHERE no_unit = 5894;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = 677,
  area_id = NULL,
  harga_sewa_bulanan = 13250000.0,
  on_hire_date = '2025-01-07',
  rate_changed_at = NOW()
WHERE no_unit = 3848;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 248,
  kontrak_id = 432,
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
  customer_location_id = 1004,
  kontrak_id = 368,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3851;
UPDATE inventory_unit SET
  customer_id = 84,
  customer_location_id = 1005,
  kontrak_id = 368,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3852;
UPDATE inventory_unit SET
  customer_id = 69,
  customer_location_id = 213,
  kontrak_id = 678,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = '2024-07-31',
  rate_changed_at = NOW()
WHERE no_unit = 3853;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = 926,
  kontrak_id = 679,
  area_id = 20,
  harga_sewa_bulanan = 28500000.0,
  on_hire_date = '2024-10-07',
  rate_changed_at = NOW()
WHERE no_unit = 3854;
UPDATE inventory_unit SET
  customer_id = 69,
  customer_location_id = 213,
  kontrak_id = 680,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = '2024-07-31',
  rate_changed_at = NOW()
WHERE no_unit = 3855;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = 607,
  area_id = NULL,
  harga_sewa_bulanan = 6950000.0,
  on_hire_date = '2024-12-23',
  rate_changed_at = NOW()
WHERE no_unit = 3856;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = 675,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5907;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = @location_27,
  kontrak_id = 681,
  area_id = NULL,
  harga_sewa_bulanan = 15300000.0,
  on_hire_date = '2024-06-25',
  rate_changed_at = NOW()
WHERE no_unit = 3860;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 5200000.0,
  on_hire_date = '2025-02-20',
  rate_changed_at = NOW()
WHERE no_unit = 3861;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 5200000.0,
  on_hire_date = '2025-02-20',
  rate_changed_at = NOW()
WHERE no_unit = 3862;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 5200000.0,
  on_hire_date = '2025-02-20',
  rate_changed_at = NOW()
WHERE no_unit = 3863;
UPDATE inventory_unit SET
  customer_id = 177,
  customer_location_id = 422,
  kontrak_id = 376,
  area_id = 21,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1816;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 345,
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 5200000.0,
  on_hire_date = '2025-02-20',
  rate_changed_at = NOW()
WHERE no_unit = 3864;
UPDATE inventory_unit SET
  customer_id = 44,
  customer_location_id = 122,
  kontrak_id = 682,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = '2025-08-20',
  rate_changed_at = NOW()
WHERE no_unit = 3867;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = 423,
  area_id = NULL,
  harga_sewa_bulanan = 10500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5916;
UPDATE inventory_unit SET
  customer_id = 174,
  customer_location_id = 416,
  kontrak_id = 483,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3869;
UPDATE inventory_unit SET
  customer_id = 110,
  customer_location_id = 313,
  kontrak_id = 683,
  area_id = NULL,
  harga_sewa_bulanan = 14800000.0,
  on_hire_date = '2024-06-04',
  rate_changed_at = NOW()
WHERE no_unit = 3870;
UPDATE inventory_unit SET
  customer_id = 49,
  customer_location_id = 162,
  kontrak_id = 684,
  area_id = NULL,
  harga_sewa_bulanan = 9739681.0,
  on_hire_date = '2024-11-15',
  rate_changed_at = NOW()
WHERE no_unit = 3874;

-- Processed 1000/2178 units

UPDATE inventory_unit SET
  customer_id = 229,
  customer_location_id = 929,
  kontrak_id = 402,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1828;
UPDATE inventory_unit SET
  customer_id = 64,
  customer_location_id = 191,
  kontrak_id = 624,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = '2024-05-16',
  rate_changed_at = NOW()
WHERE no_unit = 3878;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 182,
  kontrak_id = 448,
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
  customer_location_id = 1007,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5933;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = @location_15,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 10900000.0,
  on_hire_date = '2025-11-04',
  rate_changed_at = NOW()
WHERE no_unit = 3888;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 419,
  kontrak_id = 402,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3892;
UPDATE inventory_unit SET
  customer_id = 176,
  customer_location_id = 421,
  kontrak_id = 402,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3893;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 237,
  kontrak_id = 685,
  area_id = NULL,
  harga_sewa_bulanan = 17800000.0,
  on_hire_date = '2023-11-23',
  rate_changed_at = NOW()
WHERE no_unit = 3902;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 240,
  kontrak_id = 566,
  area_id = NULL,
  harga_sewa_bulanan = 17000000.0,
  on_hire_date = '2023-02-27',
  rate_changed_at = NOW()
WHERE no_unit = 3906;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 21,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 3912;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 21,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-12-22',
  rate_changed_at = NOW()
WHERE no_unit = 3913;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 22,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 3914;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 25,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 3915;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 26,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 3916;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 28,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 10800000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 3917;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 42,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 10800000.0,
  on_hire_date = '2023-12-21',
  rate_changed_at = NOW()
WHERE no_unit = 3918;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 29,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 3919;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 30,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-12-28',
  rate_changed_at = NOW()
WHERE no_unit = 3920;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 31,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-12-28',
  rate_changed_at = NOW()
WHERE no_unit = 3921;
UPDATE inventory_unit SET
  customer_id = 100,
  customer_location_id = @location_17,
  kontrak_id = 686,
  area_id = NULL,
  harga_sewa_bulanan = 7250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3922;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = 956,
  kontrak_id = 687,
  area_id = NULL,
  harga_sewa_bulanan = 39900000.0,
  on_hire_date = '2024-10-29',
  rate_changed_at = NOW()
WHERE no_unit = 3924;
UPDATE inventory_unit SET
  customer_id = 211,
  customer_location_id = 1008,
  kontrak_id = 688,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2024-09-28',
  rate_changed_at = NOW()
WHERE no_unit = 3927;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = 232,
  kontrak_id = 689,
  area_id = NULL,
  harga_sewa_bulanan = 38900000.0,
  on_hire_date = '2024-10-28',
  rate_changed_at = NOW()
WHERE no_unit = 3930;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = 232,
  kontrak_id = 690,
  area_id = NULL,
  harga_sewa_bulanan = 38900000.0,
  on_hire_date = '2024-10-28',
  rate_changed_at = NOW()
WHERE no_unit = 3931;
UPDATE inventory_unit SET
  customer_id = 199,
  customer_location_id = @location_28,
  kontrak_id = 368,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = '2023-10-02',
  rate_changed_at = NOW()
WHERE no_unit = 1885;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = 232,
  kontrak_id = 689,
  area_id = NULL,
  harga_sewa_bulanan = 38900000.0,
  on_hire_date = '2024-10-28',
  rate_changed_at = NOW()
WHERE no_unit = 3934;
UPDATE inventory_unit SET
  customer_id = 62,
  customer_location_id = 1003,
  kontrak_id = 691,
  area_id = 12,
  harga_sewa_bulanan = 26500000.0,
  on_hire_date = '2025-10-20',
  rate_changed_at = NOW()
WHERE no_unit = 5985;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = 1010,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3938;
UPDATE inventory_unit SET
  customer_id = 100,
  customer_location_id = @location_17,
  kontrak_id = 692,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3939;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = 675,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5990;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = 675,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5992;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 351,
  kontrak_id = 693,
  area_id = NULL,
  harga_sewa_bulanan = 6800000.0,
  on_hire_date = '2025-10-22',
  rate_changed_at = NOW()
WHERE no_unit = 5994;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 351,
  kontrak_id = 693,
  area_id = NULL,
  harga_sewa_bulanan = 6800000.0,
  on_hire_date = '2025-10-22',
  rate_changed_at = NOW()
WHERE no_unit = 5995;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_29,
  kontrak_id = 694,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6000;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_29,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6001;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_29,
  kontrak_id = 694,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6002;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = 926,
  kontrak_id = 695,
  area_id = 20,
  harga_sewa_bulanan = 48500000.0,
  on_hire_date = '2024-10-07',
  rate_changed_at = NOW()
WHERE no_unit = 3955;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_2,
  kontrak_id = 696,
  area_id = 35,
  harga_sewa_bulanan = 22500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3956;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_29,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6003;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_29,
  kontrak_id = @contract_1,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6004;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_29,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6006;
UPDATE inventory_unit SET
  customer_id = 7,
  customer_location_id = @location_11,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 58000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3960;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_29,
  kontrak_id = 694,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6007;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_29,
  kontrak_id = 694,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6009;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = 1012,
  kontrak_id = 675,
  area_id = NULL,
  harga_sewa_bulanan = 6800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6011;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_29,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6013;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_29,
  kontrak_id = 694,
  area_id = NULL,
  harga_sewa_bulanan = 7800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6014;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_29,
  kontrak_id = 694,
  area_id = NULL,
  harga_sewa_bulanan = 16000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6015;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_29,
  kontrak_id = 694,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6018;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 971,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2024-06-10',
  rate_changed_at = NOW()
WHERE no_unit = 3971;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = @location_29,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6020;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = 1013,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2024-07-17',
  rate_changed_at = NOW()
WHERE no_unit = 3974;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_5,
  kontrak_id = 697,
  area_id = 19,
  harga_sewa_bulanan = 7100000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6023;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_5,
  kontrak_id = 698,
  area_id = 19,
  harga_sewa_bulanan = 7100000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6024;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = @location_5,
  kontrak_id = 699,
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
  customer_location_id = 971,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = '2024-10-24',
  rate_changed_at = NOW()
WHERE no_unit = 1931;
UPDATE inventory_unit SET
  customer_id = 49,
  customer_location_id = 166,
  kontrak_id = 700,
  area_id = NULL,
  harga_sewa_bulanan = 14500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6026;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 126,
  kontrak_id = 607,
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
  customer_location_id = 909,
  kontrak_id = 701,
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
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 11850000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 3986;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = @location_30,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 11850000.0,
  on_hire_date = '2023-12-27',
  rate_changed_at = NOW()
WHERE no_unit = 3988;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 43,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 11850000.0,
  on_hire_date = '2023-12-21',
  rate_changed_at = NOW()
WHERE no_unit = 3989;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 35,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 11850000.0,
  on_hire_date = '2023-12-28',
  rate_changed_at = NOW()
WHERE no_unit = 3990;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 37,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 10800000.0,
  on_hire_date = '2023-12-29',
  rate_changed_at = NOW()
WHERE no_unit = 3991;
UPDATE inventory_unit SET
  customer_id = 130,
  customer_location_id = 1015,
  kontrak_id = 368,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = '2022-12-07',
  rate_changed_at = NOW()
WHERE no_unit = 1944;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 38,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-12-29',
  rate_changed_at = NOW()
WHERE no_unit = 3992;
UPDATE inventory_unit SET
  customer_id = 122,
  customer_location_id = 333,
  kontrak_id = 702,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = '2025-11-25',
  rate_changed_at = NOW()
WHERE no_unit = 1946;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 39,
  kontrak_id = 487,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = '2023-12-29',
  rate_changed_at = NOW()
WHERE no_unit = 3993;
UPDATE inventory_unit SET
  customer_id = 138,
  customer_location_id = 357,
  kontrak_id = 598,
  area_id = 19,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = '2024-03-21',
  rate_changed_at = NOW()
WHERE no_unit = 3994;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = @location_30,
  kontrak_id = 487,
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
  customer_location_id = 937,
  kontrak_id = 457,
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
  customer_location_id = 937,
  kontrak_id = 457,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3998;
UPDATE inventory_unit SET
  customer_id = 170,
  customer_location_id = 937,
  kontrak_id = 457,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3999;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = 232,
  kontrak_id = 703,
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
  kontrak_id = 704,
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
  kontrak_id = 705,
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
  customer_location_id = @location_6,
  kontrak_id = 510,
  area_id = 19,
  harga_sewa_bulanan = 16000000.0,
  on_hire_date = '2024-12-03',
  rate_changed_at = NOW()
WHERE no_unit = 1977;
UPDATE inventory_unit SET
  customer_id = 49,
  customer_location_id = 166,
  kontrak_id = 700,
  area_id = NULL,
  harga_sewa_bulanan = 14500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1978;
UPDATE inventory_unit SET
  customer_id = 25,
  customer_location_id = 84,
  kontrak_id = 706,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1979;
UPDATE inventory_unit SET
  customer_id = 103,
  customer_location_id = @location_31,
  kontrak_id = 707,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2026-01-14',
  rate_changed_at = NOW()
WHERE no_unit = 6072;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 435,
  kontrak_id = 523,
  area_id = NULL,
  harga_sewa_bulanan = 24000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5584;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 247,
  kontrak_id = 432,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = '2024-10-04',
  rate_changed_at = NOW()
WHERE no_unit = 1993;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = 223,
  kontrak_id = 594,
  area_id = NULL,
  harga_sewa_bulanan = 17300000.0,
  on_hire_date = '2025-05-13',
  rate_changed_at = NOW()
WHERE no_unit = 5615;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 928,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 6800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2001;
UPDATE inventory_unit SET
  customer_id = 68,
  customer_location_id = 1017,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2013;
UPDATE inventory_unit SET
  customer_id = 204,
  customer_location_id = 454,
  kontrak_id = 708,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2025-05-02',
  rate_changed_at = NOW()
WHERE no_unit = 2018;
UPDATE inventory_unit SET
  customer_id = 96,
  customer_location_id = 962,
  kontrak_id = 540,
  area_id = NULL,
  harga_sewa_bulanan = 11750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5286;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = 223,
  kontrak_id = 594,
  area_id = NULL,
  harga_sewa_bulanan = 17300000.0,
  on_hire_date = '2025-05-13',
  rate_changed_at = NOW()
WHERE no_unit = 5616;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = 1018,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2052;

-- Processed 1100/2178 units

UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = 15,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2057;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = 1020,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2060;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 971,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2065;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2070;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 911,
  kontrak_id = 710,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2086;
UPDATE inventory_unit SET
  customer_id = 41,
  customer_location_id = 1022,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2093;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = 1023,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2112;
UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = 1024,
  kontrak_id = 711,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2113;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = 1025,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2114;
UPDATE inventory_unit SET
  customer_id = 41,
  customer_location_id = 1022,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2115;
UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = 1024,
  kontrak_id = 712,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2117;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 911,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2130;
UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = 1024,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2138;
UPDATE inventory_unit SET
  customer_id = 99,
  customer_location_id = 1026,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2147;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 971,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2168;
UPDATE inventory_unit SET
  customer_id = 177,
  customer_location_id = 1027,
  kontrak_id = NULL,
  area_id = 21,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2169;
UPDATE inventory_unit SET
  customer_id = 29,
  customer_location_id = 1028,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2198;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 1029,
  kontrak_id = 484,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2209;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 1029,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2215;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 911,
  kontrak_id = 710,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2217;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 1029,
  kontrak_id = 484,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2227;
UPDATE inventory_unit SET
  customer_id = 166,
  customer_location_id = 1030,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2229;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = 1031,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2236;
UPDATE inventory_unit SET
  customer_id = 184,
  customer_location_id = 1032,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2237;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 1033,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2251;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = 1034,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2258;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = 1031,
  kontrak_id = 713,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2261;
UPDATE inventory_unit SET
  customer_id = 99,
  customer_location_id = 1026,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2268;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = 1034,
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
  customer_location_id = 1035,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2298;
UPDATE inventory_unit SET
  customer_id = 99,
  customer_location_id = 1026,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2299;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = 1031,
  kontrak_id = 714,
  area_id = 34,
  harga_sewa_bulanan = 10500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2300;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = 1034,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2301;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = 1034,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2303;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = 1034,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2304;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = 1034,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2305;
UPDATE inventory_unit SET
  customer_id = 98,
  customer_location_id = 1036,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2306;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = 1031,
  kontrak_id = 715,
  area_id = 33,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2307;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = 1031,
  kontrak_id = 716,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2308;
UPDATE inventory_unit SET
  customer_id = 220,
  customer_location_id = 1037,
  kontrak_id = @contract_4,
  area_id = 33,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2320;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 1029,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2324;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 1038,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2336;
UPDATE inventory_unit SET
  customer_id = 177,
  customer_location_id = 1027,
  kontrak_id = NULL,
  area_id = 21,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2350;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 1029,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2352;
UPDATE inventory_unit SET
  customer_id = 99,
  customer_location_id = 1026,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2354;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = 717,
  area_id = 27,
  harga_sewa_bulanan = 70000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2360;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = 717,
  area_id = 27,
  harga_sewa_bulanan = 70000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2361;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = 717,
  area_id = 27,
  harga_sewa_bulanan = 70000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2362;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = 1040,
  kontrak_id = 718,
  area_id = NULL,
  harga_sewa_bulanan = 13200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2369;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 1038,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2373;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 1038,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2374;
UPDATE inventory_unit SET
  customer_id = 199,
  customer_location_id = 1041,
  kontrak_id = @contract_5,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2395;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = 1018,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2408;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 971,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2410;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = 1023,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2411;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 25300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2416;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2430;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = 1034,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2445;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = 1034,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2446;
UPDATE inventory_unit SET
  customer_id = 41,
  customer_location_id = 1022,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2453;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = 1043,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2456;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2457;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2459;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2460;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2463;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2464;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2465;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2469;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2470;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2472;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = 720,
  area_id = 19,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2473;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = 1023,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2474;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2475;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2476;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2477;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2480;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2485;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 10600000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2487;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2489;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2491;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2492;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2497;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 721,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2505;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 721,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2510;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 721,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2511;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2512;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 1038,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2513;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2528;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2531;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2534;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 25300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2535;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2543;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = 1047,
  kontrak_id = 722,
  area_id = NULL,
  harga_sewa_bulanan = 15500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2551;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = 1018,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2560;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 723,
  area_id = 2,
  harga_sewa_bulanan = 14500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2570;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 1048,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2571;
UPDATE inventory_unit SET
  customer_id = 238,
  customer_location_id = 1049,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2572;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = 1047,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2573;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 1029,
  kontrak_id = 484,
  area_id = NULL,
  harga_sewa_bulanan = 30500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2574;

-- Processed 1200/2178 units

UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = 1047,
  kontrak_id = 722,
  area_id = NULL,
  harga_sewa_bulanan = 15500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2579;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 1050,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2580;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2583;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = 1052,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2584;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 724,
  area_id = 33,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2585;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = 1040,
  kontrak_id = 718,
  area_id = 19,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2593;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = NULL,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2605;
UPDATE inventory_unit SET
  customer_id = 232,
  customer_location_id = 1054,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2617;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = 1055,
  kontrak_id = NULL,
  area_id = 20,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2618;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2622;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2635;
UPDATE inventory_unit SET
  customer_id = 199,
  customer_location_id = 1041,
  kontrak_id = NULL,
  area_id = 33,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2636;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = 1018,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2641;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 1056,
  kontrak_id = 725,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2643;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = 1020,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2647;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2648;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2653;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2654;
UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = 1024,
  kontrak_id = 726,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2655;
UPDATE inventory_unit SET
  customer_id = 9,
  customer_location_id = 1057,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2656;
UPDATE inventory_unit SET
  customer_id = 94,
  customer_location_id = 1058,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2664;
UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = 1024,
  kontrak_id = 712,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2669;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 1056,
  kontrak_id = 725,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2680;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = 1023,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2688;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = 717,
  area_id = 27,
  harga_sewa_bulanan = 70000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2689;
UPDATE inventory_unit SET
  customer_id = 199,
  customer_location_id = 1041,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2695;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = 1059,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2725;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = 1059,
  kontrak_id = 727,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5021;
UPDATE inventory_unit SET
  customer_id = 7,
  customer_location_id = 1060,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2741;
UPDATE inventory_unit SET
  customer_id = 7,
  customer_location_id = 1060,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2744;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = 1020,
  kontrak_id = 728,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2745;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 1038,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2748;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = 1018,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2749;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2750;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 1029,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2752;
UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = 1024,
  kontrak_id = 711,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2763;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 1038,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2764;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 1061,
  kontrak_id = NULL,
  area_id = 35,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2768;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2772;
UPDATE inventory_unit SET
  customer_id = 9,
  customer_location_id = 1057,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2773;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2776;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2779;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2793;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2794;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2795;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2796;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2797;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2799;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2800;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2802;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2803;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 10200000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2804;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5849;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 10500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2807;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 10500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2808;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2809;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2810;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2811;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2812;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2813;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2814;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2815;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2816;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2817;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2818;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2819;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5852;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 2850000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2822;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 1038,
  kontrak_id = 730,
  area_id = NULL,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2823;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5853;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 1038,
  kontrak_id = 730,
  area_id = NULL,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2825;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2831;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2832;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5855;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2838;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2839;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5856;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5860;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 971,
  kontrak_id = 731,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2866;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 820;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 821;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 824;
UPDATE inventory_unit SET
  customer_id = 177,
  customer_location_id = 1027,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2875;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 1062,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 835;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = NULL,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2883;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 1038,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2884;
UPDATE inventory_unit SET
  customer_id = 94,
  customer_location_id = 1058,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2886;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = 1031,
  kontrak_id = 732,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2888;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 844;
UPDATE inventory_unit SET
  customer_id = 24,
  customer_location_id = 1063,
  kontrak_id = NULL,
  area_id = 21,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2905;
UPDATE inventory_unit SET
  customer_id = 49,
  customer_location_id = 1064,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2910;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2948;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2949;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2950;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5002;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5003;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 9600000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 908;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 733,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5005;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5007;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 473,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5008;

-- Processed 1300/2178 units

UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 1038,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2963;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = 1034,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2964;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = 1059,
  kontrak_id = 727,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5018;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = 1059,
  kontrak_id = 727,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5019;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 734,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2972;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 734,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2973;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 734,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2974;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 734,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2975;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 734,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2976;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = NULL,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2977;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 735,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2978;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = 1059,
  kontrak_id = 727,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5020;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 736,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2980;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 737,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2981;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = 1031,
  kontrak_id = 738,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 932;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = 1059,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5022;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = 1059,
  kontrak_id = 727,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5023;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = 1059,
  kontrak_id = 727,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5024;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2987;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5036;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2989;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 1056,
  kontrak_id = 725,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2990;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5038;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 2850000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2992;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 739,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5040;
UPDATE inventory_unit SET
  customer_id = 148,
  customer_location_id = 1065,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2998;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = 1034,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3001;
UPDATE inventory_unit SET
  customer_id = 232,
  customer_location_id = 1054,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3006;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 1038,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3015;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3016;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5072;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5076;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = 1025,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3031;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = 1031,
  kontrak_id = 740,
  area_id = 35,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3032;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5084;
UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = 1024,
  kontrak_id = 712,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3038;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 32150000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3041;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 32150000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3042;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5090;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5091;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5092;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5093;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5094;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5096;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = NULL,
  area_id = 33,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1005;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3055;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3058;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 1038,
  kontrak_id = NULL,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3060;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5110;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 1062,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1015;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5111;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = 1066,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5132;
UPDATE inventory_unit SET
  customer_id = 218,
  customer_location_id = 1067,
  kontrak_id = 741,
  area_id = NULL,
  harga_sewa_bulanan = 23500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5133;
UPDATE inventory_unit SET
  customer_id = 84,
  customer_location_id = 1068,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3091;
UPDATE inventory_unit SET
  customer_id = 99,
  customer_location_id = 1026,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3092;
UPDATE inventory_unit SET
  customer_id = 84,
  customer_location_id = 1068,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3095;
UPDATE inventory_unit SET
  customer_id = 84,
  customer_location_id = 1068,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3096;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = 1031,
  kontrak_id = 742,
  area_id = 33,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3101;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = 1020,
  kontrak_id = 743,
  area_id = NULL,
  harga_sewa_bulanan = 10300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3102;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = 1020,
  kontrak_id = 744,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3103;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 1038,
  kontrak_id = 727,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3104;
UPDATE inventory_unit SET
  customer_id = 199,
  customer_location_id = 1041,
  kontrak_id = 745,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3105;
UPDATE inventory_unit SET
  customer_id = 84,
  customer_location_id = 1068,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3106;
UPDATE inventory_unit SET
  customer_id = 199,
  customer_location_id = 1041,
  kontrak_id = 746,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1059;
UPDATE inventory_unit SET
  customer_id = 2,
  customer_location_id = 1069,
  kontrak_id = 747,
  area_id = 20,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5150;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = 1059,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5152;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = 1020,
  kontrak_id = 748,
  area_id = NULL,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3110;
UPDATE inventory_unit SET
  customer_id = 238,
  customer_location_id = 1049,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5156;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = 1031,
  kontrak_id = 749,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5160;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3120;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 750,
  area_id = 33,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5170;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3128;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = 1070,
  kontrak_id = 751,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3129;
UPDATE inventory_unit SET
  customer_id = 41,
  customer_location_id = 1022,
  kontrak_id = 752,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3130;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = 1031,
  kontrak_id = 753,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3131;
UPDATE inventory_unit SET
  customer_id = 41,
  customer_location_id = 1022,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3132;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 754,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3139;
UPDATE inventory_unit SET
  customer_id = 174,
  customer_location_id = 1071,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5193;
UPDATE inventory_unit SET
  customer_id = 220,
  customer_location_id = 1037,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5194;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 25300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3153;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3155;
UPDATE inventory_unit SET
  customer_id = 199,
  customer_location_id = 1041,
  kontrak_id = 755,
  area_id = 33,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3156;
UPDATE inventory_unit SET
  customer_id = 238,
  customer_location_id = 1049,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5207;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3160;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3161;
UPDATE inventory_unit SET
  customer_id = 34,
  customer_location_id = 1035,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3164;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5214;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = 1025,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3173;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 971,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5221;
UPDATE inventory_unit SET
  customer_id = 98,
  customer_location_id = 1036,
  kontrak_id = 756,
  area_id = NULL,
  harga_sewa_bulanan = 5500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3175;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 757,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5516;
UPDATE inventory_unit SET
  customer_id = 102,
  customer_location_id = 1072,
  kontrak_id = NULL,
  area_id = 21,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3178;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = 1020,
  kontrak_id = 758,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3185;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = 1020,
  kontrak_id = 759,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3186;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 22800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5234;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 22800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5235;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 22800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5236;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 22800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5237;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 760,
  area_id = 33,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5239;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 761,
  area_id = 13,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5240;

-- Processed 1400/2178 units

UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 762,
  area_id = 33,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5241;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 1062,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 11800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5520;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 1062,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 11800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5521;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 763,
  area_id = 33,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5251;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = 764,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5252;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 765,
  area_id = 13,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5253;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = NULL,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5254;
UPDATE inventory_unit SET
  customer_id = 84,
  customer_location_id = 1068,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3207;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3208;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = 764,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5255;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 21500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5256;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3211;
UPDATE inventory_unit SET
  customer_id = 85,
  customer_location_id = 1033,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3214;
UPDATE inventory_unit SET
  customer_id = 126,
  customer_location_id = 1073,
  kontrak_id = NULL,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3217;
UPDATE inventory_unit SET
  customer_id = 220,
  customer_location_id = 1037,
  kontrak_id = @contract_4,
  area_id = NULL,
  harga_sewa_bulanan = 16350000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5269;
UPDATE inventory_unit SET
  customer_id = 220,
  customer_location_id = 1037,
  kontrak_id = @contract_4,
  area_id = NULL,
  harga_sewa_bulanan = 16350000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5270;
UPDATE inventory_unit SET
  customer_id = 220,
  customer_location_id = 1037,
  kontrak_id = @contract_4,
  area_id = NULL,
  harga_sewa_bulanan = 16350000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5271;
UPDATE inventory_unit SET
  customer_id = 220,
  customer_location_id = 1037,
  kontrak_id = 766,
  area_id = NULL,
  harga_sewa_bulanan = 10500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5273;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1179;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1180;
UPDATE inventory_unit SET
  customer_id = 29,
  customer_location_id = 1028,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3233;
UPDATE inventory_unit SET
  customer_id = 54,
  customer_location_id = 1043,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3242;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = NULL,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5292;
UPDATE inventory_unit SET
  customer_id = 41,
  customer_location_id = 1022,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3249;
UPDATE inventory_unit SET
  customer_id = 98,
  customer_location_id = 1036,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1203;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 1061,
  kontrak_id = NULL,
  area_id = 35,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3254;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = 1034,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5303;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1208;
UPDATE inventory_unit SET
  customer_id = 203,
  customer_location_id = 1074,
  kontrak_id = 767,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5304;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = 1047,
  kontrak_id = 722,
  area_id = NULL,
  harga_sewa_bulanan = 15500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5307;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1214;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = 1040,
  kontrak_id = 718,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1215;
UPDATE inventory_unit SET
  customer_id = 195,
  customer_location_id = 1070,
  kontrak_id = 496,
  area_id = NULL,
  harga_sewa_bulanan = 5850000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3262;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 1062,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1217;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 1062,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1218;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3266;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 1038,
  kontrak_id = NULL,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3268;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = 1052,
  kontrak_id = 768,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5319;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = 1052,
  kontrak_id = 768,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5320;
UPDATE inventory_unit SET
  customer_id = 220,
  customer_location_id = 1037,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5323;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 21500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5329;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3286;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3287;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3289;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 1062,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1247;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 1062,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1248;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 1062,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1249;
UPDATE inventory_unit SET
  customer_id = 238,
  customer_location_id = 1049,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3300;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = 1031,
  kontrak_id = 769,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3301;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3302;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = 770,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3305;
UPDATE inventory_unit SET
  customer_id = 199,
  customer_location_id = 1041,
  kontrak_id = 771,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1258;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = 772,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3306;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = 772,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3307;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 773,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5357;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 1062,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1262;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 774,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5358;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 775,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5359;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 1062,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1265;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 1062,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1266;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 1062,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1267;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 10100000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1268;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = 1023,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3315;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 911,
  kontrak_id = 710,
  area_id = NULL,
  harga_sewa_bulanan = 13800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1270;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = 1031,
  kontrak_id = 776,
  area_id = 33,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3318;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 25300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5360;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 25300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5361;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 1038,
  kontrak_id = 727,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3323;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 1038,
  kontrak_id = 727,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3324;
UPDATE inventory_unit SET
  customer_id = 49,
  customer_location_id = 1064,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3327;
UPDATE inventory_unit SET
  customer_id = 99,
  customer_location_id = 1026,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3328;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 1062,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1281;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 1062,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1282;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = 1040,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3329;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = 1034,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5375;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 777,
  area_id = 13,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5377;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 778,
  area_id = 13,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5378;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 1062,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1287;
UPDATE inventory_unit SET
  customer_id = 237,
  customer_location_id = 1075,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5382;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = 1052,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5385;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 779,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5387;
UPDATE inventory_unit SET
  customer_id = 38,
  customer_location_id = 1076,
  kontrak_id = 780,
  area_id = NULL,
  harga_sewa_bulanan = 8600000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5388;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5389;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 23800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5390;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = 1052,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5396;
UPDATE inventory_unit SET
  customer_id = 99,
  customer_location_id = 1026,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1301;
UPDATE inventory_unit SET
  customer_id = 36,
  customer_location_id = 1077,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5403;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = 1047,
  kontrak_id = 722,
  area_id = NULL,
  harga_sewa_bulanan = 15500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5404;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 739,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5407;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 971,
  kontrak_id = 731,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5409;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 26800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5410;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 26800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5411;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5412;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = NULL,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3366;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = 1031,
  kontrak_id = 781,
  area_id = 33,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3367;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = 1047,
  kontrak_id = 722,
  area_id = NULL,
  harga_sewa_bulanan = 15500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5414;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = 1052,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5415;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3371;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = 1034,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3372;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 782,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5426;

-- Processed 1500/2178 units

UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = 1024,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1337;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5444;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 22800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5446;
UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = 1024,
  kontrak_id = 712,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1351;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = 1034,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3399;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 22800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5447;
UPDATE inventory_unit SET
  customer_id = 33,
  customer_location_id = 1078,
  kontrak_id = 783,
  area_id = NULL,
  harga_sewa_bulanan = 32500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3402;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 22800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5448;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5451;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5452;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1358;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1359;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1360;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1361;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = 1034,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3406;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = 1034,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3407;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = 1034,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3408;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5453;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5454;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5455;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = 35,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5456;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5457;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = 35,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5458;
UPDATE inventory_unit SET
  customer_id = 208,
  customer_location_id = 1079,
  kontrak_id = 784,
  area_id = NULL,
  harga_sewa_bulanan = 29000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5462;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 22800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5465;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3421;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 785,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5466;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 7400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1381;
UPDATE inventory_unit SET
  customer_id = 174,
  customer_location_id = 1071,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3429;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3430;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3433;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 1062,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1386;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3435;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5484;
UPDATE inventory_unit SET
  customer_id = 238,
  customer_location_id = 1049,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1389;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5485;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5486;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 26800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5487;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 473,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3441;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3442;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 26800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5488;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 26800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5489;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 1080,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1397;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = 786,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5490;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = 787,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5491;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5492;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5493;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5494;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 1048,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3451;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5495;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5496;
UPDATE inventory_unit SET
  customer_id = 56,
  customer_location_id = 1048,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3454;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5497;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = 35,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5498;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 1062,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1409;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1410;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 788,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5500;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 789,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5503;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3461;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 790,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5504;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 791,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5505;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 792,
  area_id = NULL,
  harga_sewa_bulanan = 6250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5506;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = 1020,
  kontrak_id = 793,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3466;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = 1020,
  kontrak_id = 794,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3467;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1420;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1421;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = 1020,
  kontrak_id = 795,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3468;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3470;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 796,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5514;
UPDATE inventory_unit SET
  customer_id = 91,
  customer_location_id = 1080,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1425;
UPDATE inventory_unit SET
  customer_id = 238,
  customer_location_id = 1049,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1426;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 797,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5515;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3476;
UPDATE inventory_unit SET
  customer_id = 238,
  customer_location_id = 1049,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1429;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5519;
UPDATE inventory_unit SET
  customer_id = 238,
  customer_location_id = 1049,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1431;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3479;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3480;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 798,
  area_id = NULL,
  harga_sewa_bulanan = 7900000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3482;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = 1040,
  kontrak_id = 718,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3484;
UPDATE inventory_unit SET
  customer_id = 51,
  customer_location_id = 1029,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1437;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3486;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 971,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5532;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = 15,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3488;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3489;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = 35,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3490;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 799,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5533;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = 1047,
  kontrak_id = 800,
  area_id = NULL,
  harga_sewa_bulanan = 24500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3492;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = 1047,
  kontrak_id = 800,
  area_id = NULL,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3493;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 799,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5534;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 799,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5535;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 799,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5536;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = 1020,
  kontrak_id = 801,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3497;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = 764,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5537;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 779,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5538;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = 764,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5540;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3501;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = 764,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5541;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = 764,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5542;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 779,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5550;

-- Processed 1600/2178 units

UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 779,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5551;
UPDATE inventory_unit SET
  customer_id = 99,
  customer_location_id = 1026,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1458;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 779,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5552;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 802,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5553;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 779,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5554;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 782,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5555;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 782,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5556;
UPDATE inventory_unit SET
  customer_id = 196,
  customer_location_id = 1081,
  kontrak_id = 803,
  area_id = NULL,
  harga_sewa_bulanan = 29000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5557;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = 764,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5558;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1466;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1467;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1468;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1469;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1470;
UPDATE inventory_unit SET
  customer_id = 131,
  customer_location_id = 1082,
  kontrak_id = 804,
  area_id = 19,
  harga_sewa_bulanan = 9250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5567;
UPDATE inventory_unit SET
  customer_id = 131,
  customer_location_id = 1082,
  kontrak_id = 804,
  area_id = 19,
  harga_sewa_bulanan = 9250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5568;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5569;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = 1083,
  kontrak_id = 805,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5572;
UPDATE inventory_unit SET
  customer_id = 33,
  customer_location_id = 1078,
  kontrak_id = 783,
  area_id = NULL,
  harga_sewa_bulanan = 32500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3525;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = 1083,
  kontrak_id = 806,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5573;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = 1034,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1479;
UPDATE inventory_unit SET
  customer_id = 76,
  customer_location_id = 1083,
  kontrak_id = 806,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5574;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = 1034,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1481;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = 1031,
  kontrak_id = 807,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1482;
UPDATE inventory_unit SET
  customer_id = 233,
  customer_location_id = 1084,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3536;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 734,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5587;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = 1020,
  kontrak_id = 808,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3540;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 735,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5588;
UPDATE inventory_unit SET
  customer_id = 220,
  customer_location_id = 1037,
  kontrak_id = @contract_4,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3542;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = 764,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5590;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = 764,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5591;
UPDATE inventory_unit SET
  customer_id = 98,
  customer_location_id = 1036,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1497;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = 764,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5592;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = 764,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5593;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = 1034,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1500;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = 1034,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3549;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = 1031,
  kontrak_id = 809,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3550;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = 1023,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5594;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = 1020,
  kontrak_id = 810,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5597;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = 1059,
  kontrak_id = 811,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3553;
UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = 1024,
  kontrak_id = 712,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1506;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = 1040,
  kontrak_id = 718,
  area_id = NULL,
  harga_sewa_bulanan = 13500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3555;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 812,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3556;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = 1020,
  kontrak_id = 813,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5598;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = 1047,
  kontrak_id = 800,
  area_id = NULL,
  harga_sewa_bulanan = 15500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5600;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = 1047,
  kontrak_id = 800,
  area_id = NULL,
  harga_sewa_bulanan = 15500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5601;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3560;
UPDATE inventory_unit SET
  customer_id = 236,
  customer_location_id = 1085,
  kontrak_id = 814,
  area_id = 13,
  harga_sewa_bulanan = 9500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5603;
UPDATE inventory_unit SET
  customer_id = 41,
  customer_location_id = 1022,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3567;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = 717,
  area_id = 27,
  harga_sewa_bulanan = 48500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5621;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = 1020,
  kontrak_id = 815,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3575;
UPDATE inventory_unit SET
  customer_id = 220,
  customer_location_id = 1037,
  kontrak_id = @contract_4,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1529;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 1038,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5632;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5633;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5639;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5640;
UPDATE inventory_unit SET
  customer_id = 143,
  customer_location_id = 1062,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5641;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = 1020,
  kontrak_id = 815,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3599;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = 1052,
  kontrak_id = 816,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5648;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = 1020,
  kontrak_id = 815,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3601;
UPDATE inventory_unit SET
  customer_id = 41,
  customer_location_id = 1022,
  kontrak_id = 817,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3602;
UPDATE inventory_unit SET
  customer_id = 41,
  customer_location_id = 1022,
  kontrak_id = 817,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3603;
UPDATE inventory_unit SET
  customer_id = 41,
  customer_location_id = 1022,
  kontrak_id = 817,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3604;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = 1047,
  kontrak_id = 800,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5649;
UPDATE inventory_unit SET
  customer_id = 222,
  customer_location_id = 1086,
  kontrak_id = 818,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5653;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1559;
UPDATE inventory_unit SET
  customer_id = 6,
  customer_location_id = 1025,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3608;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = 1040,
  kontrak_id = 718,
  area_id = NULL,
  harga_sewa_bulanan = 14500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5654;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = 1047,
  kontrak_id = 800,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5660;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = 1047,
  kontrak_id = 800,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5661;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = 1020,
  kontrak_id = 815,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3614;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = 1047,
  kontrak_id = 800,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5662;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 1038,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1569;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = 1040,
  kontrak_id = 718,
  area_id = 19,
  harga_sewa_bulanan = 14700000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3621;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = 1047,
  kontrak_id = 800,
  area_id = NULL,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5671;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = 1047,
  kontrak_id = 800,
  area_id = NULL,
  harga_sewa_bulanan = 21000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5672;
UPDATE inventory_unit SET
  customer_id = 46,
  customer_location_id = 911,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3626;
UPDATE inventory_unit SET
  customer_id = 118,
  customer_location_id = 1087,
  kontrak_id = @contract_6,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1580;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = 1040,
  kontrak_id = 718,
  area_id = NULL,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3629;
UPDATE inventory_unit SET
  customer_id = 52,
  customer_location_id = 1088,
  kontrak_id = 820,
  area_id = NULL,
  harga_sewa_bulanan = 6750000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3631;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = 1055,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3635;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = 764,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5683;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = 1020,
  kontrak_id = 815,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3637;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = NULL,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5685;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = NULL,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5686;
UPDATE inventory_unit SET
  customer_id = 196,
  customer_location_id = 1081,
  kontrak_id = 821,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3640;
UPDATE inventory_unit SET
  customer_id = 198,
  customer_location_id = 1089,
  kontrak_id = 822,
  area_id = 17,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3643;
UPDATE inventory_unit SET
  customer_id = 2,
  customer_location_id = 1069,
  kontrak_id = NULL,
  area_id = 20,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3644;
UPDATE inventory_unit SET
  customer_id = 220,
  customer_location_id = 1037,
  kontrak_id = @contract_4,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3647;
UPDATE inventory_unit SET
  customer_id = 41,
  customer_location_id = 1022,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3649;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = 1059,
  kontrak_id = 823,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5697;
UPDATE inventory_unit SET
  customer_id = 60,
  customer_location_id = 1090,
  kontrak_id = 824,
  area_id = NULL,
  harga_sewa_bulanan = 7000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5698;
UPDATE inventory_unit SET
  customer_id = 60,
  customer_location_id = 1090,
  kontrak_id = 825,
  area_id = NULL,
  harga_sewa_bulanan = 20000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5701;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5702;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = 1020,
  kontrak_id = 826,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3655;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = 1059,
  kontrak_id = 823,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5705;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = 1059,
  kontrak_id = 823,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5706;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = 1059,
  kontrak_id = 823,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5707;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = 1059,
  kontrak_id = 823,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5708;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = 1059,
  kontrak_id = 823,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5709;

-- Processed 1700/2178 units

UPDATE inventory_unit SET
  customer_id = 29,
  customer_location_id = 1028,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3662;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = 1059,
  kontrak_id = 823,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5710;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3664;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = 1059,
  kontrak_id = 823,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5711;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1618;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = 1059,
  kontrak_id = 823,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5712;
UPDATE inventory_unit SET
  customer_id = 196,
  customer_location_id = 1081,
  kontrak_id = 827,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3668;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = 1059,
  kontrak_id = 823,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5715;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = 1059,
  kontrak_id = 823,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5716;
UPDATE inventory_unit SET
  customer_id = 198,
  customer_location_id = 1089,
  kontrak_id = 822,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3671;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = 1059,
  kontrak_id = 823,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5717;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = 1059,
  kontrak_id = 823,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5720;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = 1023,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3674;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = 1052,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5721;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = 1052,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5726;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = 1047,
  kontrak_id = 722,
  area_id = NULL,
  harga_sewa_bulanan = 15500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5727;
UPDATE inventory_unit SET
  customer_id = 60,
  customer_location_id = 1090,
  kontrak_id = NULL,
  area_id = 2,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5728;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = 1052,
  kontrak_id = 828,
  area_id = NULL,
  harga_sewa_bulanan = 4700000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5730;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = 1052,
  kontrak_id = 828,
  area_id = NULL,
  harga_sewa_bulanan = 4700000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5731;
UPDATE inventory_unit SET
  customer_id = 98,
  customer_location_id = 1036,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1636;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = 1052,
  kontrak_id = 828,
  area_id = NULL,
  harga_sewa_bulanan = 4700000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5732;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = 1031,
  kontrak_id = 829,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5733;
UPDATE inventory_unit SET
  customer_id = 203,
  customer_location_id = 1074,
  kontrak_id = 830,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5735;
UPDATE inventory_unit SET
  customer_id = 83,
  customer_location_id = 1091,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3689;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5750;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 721,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3705;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1658;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = 1031,
  kontrak_id = 831,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1661;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = 1031,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3710;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = 1040,
  kontrak_id = 718,
  area_id = 19,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3714;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = 1052,
  kontrak_id = 832,
  area_id = NULL,
  harga_sewa_bulanan = 4250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5762;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 971,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3716;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 798,
  area_id = NULL,
  harga_sewa_bulanan = 8100000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1671;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1678;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 6900000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5777;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5783;
UPDATE inventory_unit SET
  customer_id = 239,
  customer_location_id = 1092,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5784;
UPDATE inventory_unit SET
  customer_id = 239,
  customer_location_id = 1092,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5785;
UPDATE inventory_unit SET
  customer_id = 196,
  customer_location_id = 1081,
  kontrak_id = 833,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3738;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = NULL,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5787;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = NULL,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5788;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = NULL,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5789;
UPDATE inventory_unit SET
  customer_id = 224,
  customer_location_id = 1039,
  kontrak_id = NULL,
  area_id = 27,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5790;
UPDATE inventory_unit SET
  customer_id = 231,
  customer_location_id = 1093,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3746;
UPDATE inventory_unit SET
  customer_id = 60,
  customer_location_id = 1090,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5795;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 834,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5796;
UPDATE inventory_unit SET
  customer_id = 231,
  customer_location_id = 1093,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3751;
UPDATE inventory_unit SET
  customer_id = 73,
  customer_location_id = 1094,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1716;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5815;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 835,
  area_id = 33,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5817;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = 1052,
  kontrak_id = 836,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5818;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = 1052,
  kontrak_id = 836,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5819;
UPDATE inventory_unit SET
  customer_id = 83,
  customer_location_id = 1091,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3773;
UPDATE inventory_unit SET
  customer_id = 83,
  customer_location_id = 1091,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3774;
UPDATE inventory_unit SET
  customer_id = 83,
  customer_location_id = 1091,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3775;
UPDATE inventory_unit SET
  customer_id = 2,
  customer_location_id = 1069,
  kontrak_id = 837,
  area_id = 20,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3776;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 12500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5824;
UPDATE inventory_unit SET
  customer_id = 196,
  customer_location_id = 1081,
  kontrak_id = 827,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3778;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = 1055,
  kontrak_id = NULL,
  area_id = 20,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1731;
UPDATE inventory_unit SET
  customer_id = 198,
  customer_location_id = 1089,
  kontrak_id = 838,
  area_id = 17,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3780;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5825;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5826;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5828;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5829;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5830;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5831;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5832;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5833;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3789;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5834;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5835;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5836;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5837;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5838;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5839;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5840;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5841;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5842;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5843;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5844;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5845;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5846;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5847;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5848;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = 1040,
  kontrak_id = 718,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3805;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = 1040,
  kontrak_id = 718,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3806;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = 1040,
  kontrak_id = 718,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3807;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = 1040,
  kontrak_id = 718,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3808;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = 1040,
  kontrak_id = 718,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3809;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = 1040,
  kontrak_id = 718,
  area_id = 19,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3810;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5850;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5851;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3813;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3814;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5854;
UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = 1024,
  kontrak_id = 712,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1768;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3816;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5857;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5858;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5859;

-- Processed 1800/2178 units

UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3821;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5861;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5862;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5863;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5864;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5867;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5868;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5869;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3829;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5870;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5871;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5872;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5873;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5874;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5875;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5876;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5877;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5878;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 6900000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3839;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5879;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5880;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5881;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5883;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5884;
UPDATE inventory_unit SET
  customer_id = 190,
  customer_location_id = 1050,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5885;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5886;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5887;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5888;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5889;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = 1052,
  kontrak_id = 839,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5891;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = 1047,
  kontrak_id = 722,
  area_id = NULL,
  harga_sewa_bulanan = 15500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5892;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = 1020,
  kontrak_id = 840,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5896;
UPDATE inventory_unit SET
  customer_id = 77,
  customer_location_id = 1020,
  kontrak_id = 841,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5897;
UPDATE inventory_unit SET
  customer_id = 199,
  customer_location_id = 1041,
  kontrak_id = 842,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1807;
UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = 1024,
  kontrak_id = 712,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1809;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 1038,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1812;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = NULL,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1814;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 1061,
  kontrak_id = NULL,
  area_id = 35,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3865;
UPDATE inventory_unit SET
  customer_id = 60,
  customer_location_id = 1090,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5917;
UPDATE inventory_unit SET
  customer_id = 230,
  customer_location_id = 1095,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1824;
UPDATE inventory_unit SET
  customer_id = 16,
  customer_location_id = 1018,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1827;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = 1031,
  kontrak_id = 843,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3876;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3877;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 1056,
  kontrak_id = 844,
  area_id = NULL,
  harga_sewa_bulanan = 9500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3881;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3883;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 9800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3885;
UPDATE inventory_unit SET
  customer_id = 207,
  customer_location_id = 1096,
  kontrak_id = 721,
  area_id = NULL,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3886;
UPDATE inventory_unit SET
  customer_id = 142,
  customer_location_id = 1097,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3887;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 845,
  area_id = 17,
  harga_sewa_bulanan = 12250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3889;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 846,
  area_id = 2,
  harga_sewa_bulanan = 14500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3890;
UPDATE inventory_unit SET
  customer_id = 199,
  customer_location_id = 1041,
  kontrak_id = @contract_5,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1843;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = 1059,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1847;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 847,
  area_id = 17,
  harga_sewa_bulanan = 12250000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3895;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 11800000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3899;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = 1031,
  kontrak_id = 848,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3900;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = 1040,
  kontrak_id = 718,
  area_id = 19,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3901;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = 1051,
  kontrak_id = NULL,
  area_id = 26,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3910;
UPDATE inventory_unit SET
  customer_id = 140,
  customer_location_id = 1098,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1868;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5965;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5966;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5967;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5968;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5969;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5970;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5971;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 849,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3925;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 849,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3926;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5973;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 971,
  kontrak_id = 731,
  area_id = NULL,
  harga_sewa_bulanan = 9450000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3928;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 850,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3929;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5974;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5975;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5976;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 849,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3933;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5977;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3935;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = 849,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3936;
UPDATE inventory_unit SET
  customer_id = 198,
  customer_location_id = 1089,
  kontrak_id = 851,
  area_id = NULL,
  harga_sewa_bulanan = 8500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3937;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = 1047,
  kontrak_id = 852,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5978;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 27500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5986;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = 1052,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5987;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = 1052,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5988;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = 1052,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5991;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 1038,
  kontrak_id = 730,
  area_id = NULL,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3945;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5993;
UPDATE inventory_unit SET
  customer_id = 166,
  customer_location_id = 1030,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1900;
UPDATE inventory_unit SET
  customer_id = 8,
  customer_location_id = 1047,
  kontrak_id = 852,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5997;
UPDATE inventory_unit SET
  customer_id = 92,
  customer_location_id = 1023,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3953;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3957;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 709,
  area_id = 18,
  harga_sewa_bulanan = 7300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3958;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = 853,
  area_id = 17,
  harga_sewa_bulanan = 16500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3959;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = 1099,
  kontrak_id = 694,
  area_id = NULL,
  harga_sewa_bulanan = 11000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6005;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = 1099,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6008;
UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = 1024,
  kontrak_id = 712,
  area_id = NULL,
  harga_sewa_bulanan = 6000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1914;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = 1052,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6012;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 854,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6016;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 854,
  area_id = NULL,
  harga_sewa_bulanan = 14000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6017;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = 1099,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6019;
UPDATE inventory_unit SET
  customer_id = 47,
  customer_location_id = 1099,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6021;
UPDATE inventory_unit SET
  customer_id = 219,
  customer_location_id = 1059,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6022;

-- Processed 1900/2178 units

UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = NULL,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1927;
UPDATE inventory_unit SET
  customer_id = 41,
  customer_location_id = 1022,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1929;
UPDATE inventory_unit SET
  customer_id = 39,
  customer_location_id = 1024,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3978;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3979;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = 1052,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6028;
UPDATE inventory_unit SET
  customer_id = 129,
  customer_location_id = 1100,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1933;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 971,
  kontrak_id = 477,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1934;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3983;
UPDATE inventory_unit SET
  customer_id = 70,
  customer_location_id = 1040,
  kontrak_id = 718,
  area_id = 19,
  harga_sewa_bulanan = 13000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3984;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 1038,
  kontrak_id = NULL,
  area_id = 18,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1937;
UPDATE inventory_unit SET
  customer_id = 101,
  customer_location_id = 1045,
  kontrak_id = 855,
  area_id = NULL,
  harga_sewa_bulanan = 10000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3987;
UPDATE inventory_unit SET
  customer_id = 32,
  customer_location_id = 1052,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6038;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = 35,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6039;
UPDATE inventory_unit SET
  customer_id = 20,
  customer_location_id = 1019,
  kontrak_id = NULL,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6040;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 1038,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1945;
UPDATE inventory_unit SET
  customer_id = 14,
  customer_location_id = 959,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6041;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6042;
UPDATE inventory_unit SET
  customer_id = 10,
  customer_location_id = 1044,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6043;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = 1031,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 9300000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1950;
UPDATE inventory_unit SET
  customer_id = 66,
  customer_location_id = 1021,
  kontrak_id = 729,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1951;
UPDATE inventory_unit SET
  customer_id = 4,
  customer_location_id = @location_30,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1953;
UPDATE inventory_unit SET
  customer_id = 22,
  customer_location_id = 1031,
  kontrak_id = 856,
  area_id = 26,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1955;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6051;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6052;
UPDATE inventory_unit SET
  customer_id = 2,
  customer_location_id = 1069,
  kontrak_id = NULL,
  area_id = 20,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6054;
UPDATE inventory_unit SET
  customer_id = 95,
  customer_location_id = 1053,
  kontrak_id = NULL,
  area_id = 17,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1963;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6062;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6064;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 1061,
  kontrak_id = NULL,
  area_id = 35,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1972;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6075;
UPDATE inventory_unit SET
  customer_id = 15,
  customer_location_id = 1046,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6076;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 1038,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1983;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 1038,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1984;
UPDATE inventory_unit SET
  customer_id = 2,
  customer_location_id = 1069,
  kontrak_id = 857,
  area_id = 20,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 6085;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 1038,
  kontrak_id = NULL,
  area_id = 34,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 1990;
UPDATE inventory_unit SET
  customer_id = 99,
  customer_location_id = 1026,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2019;
UPDATE inventory_unit SET
  customer_id = 87,
  customer_location_id = 971,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2021;
UPDATE inventory_unit SET
  customer_id = 235,
  customer_location_id = 1034,
  kontrak_id = NULL,
  area_id = 19,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2034;
UPDATE inventory_unit SET
  customer_id = 58,
  customer_location_id = 1042,
  kontrak_id = 720,
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
  customer_location_id = 952,
  kontrak_id = 498,
  area_id = 26,
  harga_sewa_bulanan = 3170000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = 858,
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
  kontrak_id = 859,
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
  kontrak_id = 860,
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
  kontrak_id = 861,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5655;
UPDATE inventory_unit SET
  customer_id = 191,
  customer_location_id = 436,
  kontrak_id = 861,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5656;
UPDATE inventory_unit SET
  customer_id = 148,
  customer_location_id = 386,
  kontrak_id = 862,
  area_id = NULL,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 26;
UPDATE inventory_unit SET
  customer_id = 191,
  customer_location_id = 436,
  kontrak_id = 861,
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
  kontrak_id = 863,
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
  kontrak_id = 400,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = '2024-06-13',
  rate_changed_at = NOW()
WHERE no_unit = 3627;
UPDATE inventory_unit SET
  customer_id = 19,
  customer_location_id = @location_32,
  kontrak_id = 864,
  area_id = NULL,
  harga_sewa_bulanan = 13500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5168;
UPDATE inventory_unit SET
  customer_id = 197,
  customer_location_id = 1102,
  kontrak_id = 609,
  area_id = NULL,
  harga_sewa_bulanan = 32500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5681;
UPDATE inventory_unit SET
  customer_id = 197,
  customer_location_id = 1102,
  kontrak_id = 609,
  area_id = NULL,
  harga_sewa_bulanan = 15000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3638;
UPDATE inventory_unit SET
  customer_id = 3,
  customer_location_id = @location_33,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39000000.0,
  on_hire_date = '2025-06-20',
  rate_changed_at = NOW()
WHERE no_unit = 5687;
UPDATE inventory_unit SET
  customer_id = 3,
  customer_location_id = @location_33,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39000000.0,
  on_hire_date = '2025-06-20',
  rate_changed_at = NOW()
WHERE no_unit = 5688;
UPDATE inventory_unit SET
  customer_id = 81,
  customer_location_id = @location_27,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 16300000.0,
  on_hire_date = '2025-05-03',
  rate_changed_at = NOW()
WHERE no_unit = 3641;
UPDATE inventory_unit SET
  customer_id = 3,
  customer_location_id = @location_33,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39000000.0,
  on_hire_date = '2025-06-20',
  rate_changed_at = NOW()
WHERE no_unit = 5689;
UPDATE inventory_unit SET
  customer_id = 3,
  customer_location_id = @location_33,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39000000.0,
  on_hire_date = '2025-06-20',
  rate_changed_at = NOW()
WHERE no_unit = 5690;
UPDATE inventory_unit SET
  customer_id = 3,
  customer_location_id = @location_33,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39000000.0,
  on_hire_date = '2025-06-20',
  rate_changed_at = NOW()
WHERE no_unit = 5691;
UPDATE inventory_unit SET
  customer_id = 3,
  customer_location_id = @location_33,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39000000.0,
  on_hire_date = '2025-06-20',
  rate_changed_at = NOW()
WHERE no_unit = 5692;
UPDATE inventory_unit SET
  customer_id = 3,
  customer_location_id = @location_33,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39000000.0,
  on_hire_date = '2025-06-20',
  rate_changed_at = NOW()
WHERE no_unit = 5693;
UPDATE inventory_unit SET
  customer_id = 3,
  customer_location_id = @location_33,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39000000.0,
  on_hire_date = '2025-06-20',
  rate_changed_at = NOW()
WHERE no_unit = 5694;
UPDATE inventory_unit SET
  customer_id = 3,
  customer_location_id = @location_33,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39000000.0,
  on_hire_date = '2025-06-20',
  rate_changed_at = NOW()
WHERE no_unit = 5695;
UPDATE inventory_unit SET
  customer_id = 3,
  customer_location_id = @location_33,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 39000000.0,
  on_hire_date = '2025-06-20',
  rate_changed_at = NOW()
WHERE no_unit = 5696;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 865,
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
  customer_location_id = 1104,
  kontrak_id = 866,
  area_id = NULL,
  harga_sewa_bulanan = 9000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3158;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 867,
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
  kontrak_id = 868,
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
  kontrak_id = 683,
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
  kontrak_id = 869,
  area_id = NULL,
  harga_sewa_bulanan = 17600000.0,
  on_hire_date = '2025-08-08',
  rate_changed_at = NOW()
WHERE no_unit = 5740;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = 870,
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
  kontrak_id = 871,
  area_id = NULL,
  harga_sewa_bulanan = 17600000.0,
  on_hire_date = '2025-08-08',
  rate_changed_at = NOW()
WHERE no_unit = 5742;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = 872,
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
  kontrak_id = 873,
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
  kontrak_id = 863,
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
  kontrak_id = 863,
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
  customer_location_id = 1105,
  kontrak_id = 874,
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
  kontrak_id = 489,
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
  customer_location_id = 1104,
  kontrak_id = 875,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3736;
UPDATE inventory_unit SET
  customer_id = 126,
  customer_location_id = 337,
  kontrak_id = 876,
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
  kontrak_id = 877,
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
  kontrak_id = 878,
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
  kontrak_id = 448,
  area_id = NULL,
  harga_sewa_bulanan = 18500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3791;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 879,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = '2023-01-26',
  rate_changed_at = NOW()
WHERE no_unit = 3290;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = 880,
  area_id = NULL,
  harga_sewa_bulanan = 17600000.0,
  on_hire_date = '2025-08-08',
  rate_changed_at = NOW()
WHERE no_unit = 5745;
UPDATE inventory_unit SET
  customer_id = 129,
  customer_location_id = 342,
  kontrak_id = 368,
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
  kontrak_id = 881,
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
  kontrak_id = 882,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2025-05-14',
  rate_changed_at = NOW()
WHERE no_unit = 3858;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 883,
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
  kontrak_id = 402,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2842;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 881,
  area_id = NULL,
  harga_sewa_bulanan = 60000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3354;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 884,
  area_id = NULL,
  harga_sewa_bulanan = 60000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3355;
UPDATE inventory_unit SET
  customer_id = 94,
  customer_location_id = 275,
  kontrak_id = 373,
  area_id = NULL,
  harga_sewa_bulanan = 8000000.0,
  on_hire_date = '2022-01-18',
  rate_changed_at = NOW()
WHERE no_unit = 2845;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 884,
  area_id = NULL,
  harga_sewa_bulanan = 60000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3356;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 884,
  area_id = NULL,
  harga_sewa_bulanan = 60000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3357;
UPDATE inventory_unit SET
  customer_id = 61,
  customer_location_id = @location_34,
  kontrak_id = 368,
  area_id = NULL,
  harga_sewa_bulanan = 16000000.0,
  on_hire_date = '2025-12-15',
  rate_changed_at = NOW()
WHERE no_unit = 2848;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 881,
  area_id = NULL,
  harga_sewa_bulanan = 60000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3359;
UPDATE inventory_unit SET
  customer_id = 3,
  customer_location_id = @location_33,
  kontrak_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = 45000000.0,
  on_hire_date = '2025-09-02',
  rate_changed_at = NOW()
WHERE no_unit = 5914;
UPDATE inventory_unit SET
  customer_id = 3,
  customer_location_id = @location_33,
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
  kontrak_id = 885,
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
  kontrak_id = 886,
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
  kontrak_id = 887,
  area_id = NULL,
  harga_sewa_bulanan = 13950000.0,
  on_hire_date = '2025-04-22',
  rate_changed_at = NOW()
WHERE no_unit = 5418;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = 886,
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
  kontrak_id = 885,
  area_id = NULL,
  harga_sewa_bulanan = 12950000.0,
  on_hire_date = '2025-01-20',
  rate_changed_at = NOW()
WHERE no_unit = 5424;
UPDATE inventory_unit SET
  customer_id = 98,
  customer_location_id = 285,
  kontrak_id = 756,
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
  kontrak_id = 885,
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
  kontrak_id = 448,
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
  kontrak_id = 863,
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
  customer_location_id = 1107,
  kontrak_id = 888,
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
  customer_location_id = 913,
  kontrak_id = 475,
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
  kontrak_id = 448,
  area_id = NULL,
  harga_sewa_bulanan = 6500000.0,
  on_hire_date = '2024-04-15',
  rate_changed_at = NOW()
WHERE no_unit = 3453;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = 885,
  area_id = NULL,
  harga_sewa_bulanan = 13950000.0,
  on_hire_date = '2025-01-20',
  rate_changed_at = NOW()
WHERE no_unit = 3965;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = 885,
  area_id = NULL,
  harga_sewa_bulanan = 12950000.0,
  on_hire_date = '2025-01-17',
  rate_changed_at = NOW()
WHERE no_unit = 5507;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = 886,
  area_id = NULL,
  harga_sewa_bulanan = 12950000.0,
  on_hire_date = '2025-01-17',
  rate_changed_at = NOW()
WHERE no_unit = 5508;
UPDATE inventory_unit SET
  customer_id = 55,
  customer_location_id = @location_9,
  kontrak_id = 524,
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
  kontrak_id = 889,
  area_id = NULL,
  harga_sewa_bulanan = 58000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 5517;
UPDATE inventory_unit SET
  customer_id = 63,
  customer_location_id = @location_35,
  kontrak_id = 890,
  area_id = NULL,
  harga_sewa_bulanan = 52781040.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 2447;
UPDATE inventory_unit SET
  customer_id = 192,
  customer_location_id = 437,
  kontrak_id = 891,
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
  customer_location_id = @location_3,
  kontrak_id = 581,
  area_id = 26,
  harga_sewa_bulanan = 12000000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3481;
UPDATE inventory_unit SET
  customer_id = 106,
  customer_location_id = 309,
  kontrak_id = 892,
  area_id = NULL,
  harga_sewa_bulanan = 22000000.0,
  on_hire_date = '2026-01-27',
  rate_changed_at = NOW()
WHERE no_unit = 5531;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 589,
  area_id = NULL,
  harga_sewa_bulanan = 42400000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3495;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 893,
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
  kontrak_id = 894,
  area_id = NULL,
  harga_sewa_bulanan = 38700000.0,
  on_hire_date = '2023-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 3498;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 895,
  area_id = NULL,
  harga_sewa_bulanan = 37500000.0,
  on_hire_date = '2023-05-23',
  rate_changed_at = NOW()
WHERE no_unit = 3499;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 896,
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
  kontrak_id = 368,
  area_id = NULL,
  harga_sewa_bulanan = 11500000.0,
  on_hire_date = '2023-02-02',
  rate_changed_at = NOW()
WHERE no_unit = 1975;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 348,
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2025-02-22',
  rate_changed_at = NOW()
WHERE no_unit = 5559;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 347,
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2025-02-22',
  rate_changed_at = NOW()
WHERE no_unit = 5560;
UPDATE inventory_unit SET
  customer_id = 132,
  customer_location_id = 349,
  kontrak_id = 392,
  area_id = NULL,
  harga_sewa_bulanan = 7500000.0,
  on_hire_date = '2025-02-25',
  rate_changed_at = NOW()
WHERE no_unit = 5561;
UPDATE inventory_unit SET
  customer_id = 150,
  customer_location_id = 390,
  kontrak_id = 897,
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
  customer_location_id = 1109,
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
  kontrak_id = 887,
  area_id = NULL,
  harga_sewa_bulanan = 13950000.0,
  on_hire_date = '2025-04-22',
  rate_changed_at = NOW()
WHERE no_unit = 5595;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = 887,
  area_id = NULL,
  harga_sewa_bulanan = 13950000.0,
  on_hire_date = '2025-04-22',
  rate_changed_at = NOW()
WHERE no_unit = 5596;
UPDATE inventory_unit SET
  customer_id = 123,
  customer_location_id = 334,
  kontrak_id = 898,
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
  kontrak_id = 859,
  area_id = NULL,
  harga_sewa_bulanan = 30500000.0,
  on_hire_date = NULL,
  rate_changed_at = NOW()
WHERE no_unit = 3579;
UPDATE inventory_unit SET
  customer_id = 45,
  customer_location_id = 125,
  kontrak_id = 899,
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
