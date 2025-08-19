<?php

namespace App\Models;

use CodeIgniter\Model;

class UserDivisionModel extends Model
{
    protected $table = 'user_divisions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'user_id', 'division_id', 'is_head', 'assigned_by', 'assigned_at'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'user_id' => 'required|is_natural_no_zero',
        'division_id' => 'required|is_natural_no_zero',
        'is_head' => 'permit_empty|in_list[0,1]',
        'assigned_by' => 'permit_empty|is_natural_no_zero'
    ];

    /**
     * Get user divisions with division details
     */
    public function getUserDivisions($userId)
    {
        $builder = $this->db->table($this->table);
        $builder->select('user_divisions.*, divisions.name, divisions.code, divisions.description');
        $builder->join('divisions', 'divisions.id = user_divisions.division_id');
        $builder->where('user_divisions.user_id', $userId);
        $builder->orderBy('divisions.name', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get division users with user details
     */
    public function getDivisionUsers($divisionId)
    {
        $builder = $this->db->table($this->table);
        $builder->select('user_divisions.*, users.first_name, users.last_name, users.email, users.is_active');
        $builder->join('users', 'users.id = user_divisions.user_id');
        $builder->where('user_divisions.division_id', $divisionId);
        $builder->orderBy('user_divisions.is_head', 'DESC');
        $builder->orderBy('users.first_name', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get available users for division (users not in this division)
     */
    public function getAvailableUsers($divisionId)
    {
        $builder = $this->db->table('users');
        $builder->select('users.id, users.first_name, users.last_name, users.email');
        $builder->where('users.is_active', 1);
        
        // Exclude users already in this division
        $subQuery = $this->db->table($this->table)
                           ->select('user_id')
                           ->where('division_id', $divisionId);
        
        $builder->whereNotIn('users.id', $subQuery);
        $builder->orderBy('users.first_name', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Update user divisions (replace all)
     */
    public function updateUserDivisions($userId, $divisionData, $headDivisions = [], $assignedBy = null)
    {
        $this->db->transBegin();

        try {
            // Remove existing divisions
            $this->where('user_id', $userId)->delete();
            
            // Add new divisions
            $data = [];
            foreach ($divisionData as $divisionId) {
                $data[] = [
                    'user_id' => $userId,
                    'division_id' => $divisionId,
                    'is_head' => in_array($divisionId, $headDivisions) ? 1 : 0,
                    'assigned_by' => $assignedBy,
                    'assigned_at' => date('Y-m-d H:i:s')
                ];
            }
            
            if (!empty($data)) {
                $this->insertBatch($data);
            }
            
            $this->db->transCommit();
            return true;
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return false;
        }
    }

    /**
     * Assign user to division
     */
    public function assignToDivision($userId, $divisionId, $isHead = false, $assignedBy = null)
    {
        // Check if already assigned
        if ($this->where('user_id', $userId)->where('division_id', $divisionId)->first()) {
            return true; // Already assigned
        }
        
        $data = [
            'user_id' => $userId,
            'division_id' => $divisionId,
            'is_head' => $isHead ? 1 : 0,
            'assigned_by' => $assignedBy,
            'assigned_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->insert($data);
    }

    /**
     * Remove user from division
     */
    public function removeFromDivision($userId, $divisionId)
    {
        return $this->where('user_id', $userId)->where('division_id', $divisionId)->delete();
    }

    /**
     * Check if user is in division
     */
    public function userInDivision($userId, $divisionId)
    {
        return $this->where('user_id', $userId)->where('division_id', $divisionId)->first() !== null;
    }

    /**
     * Check if user is head of division
     */
    public function userIsHeadOfDivision($userId, $divisionId)
    {
        $record = $this->where('user_id', $userId)->where('division_id', $divisionId)->first();
        return $record && $record['is_head'] == 1;
    }

    /**
     * Get division heads
     */
    public function getDivisionHeads()
    {
        $builder = $this->db->table($this->table);
        $builder->select('user_divisions.*, users.first_name, users.last_name, users.email, divisions.name as division_name');
        $builder->join('users', 'users.id = user_divisions.user_id');
        $builder->join('divisions', 'divisions.id = user_divisions.division_id');
        $builder->where('user_divisions.is_head', 1);
        $builder->orderBy('divisions.name', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get users with multiple divisions
     */
    public function getUsersWithMultipleDivisions()
    {
        $builder = $this->db->table($this->table);
        $builder->select('user_id, COUNT(*) as division_count');
        $builder->groupBy('user_id');
        $builder->having('division_count >', 1);
        
        return $builder->countAllResults();
    }

    /**
     * Get user division permissions
     */
    public function getUserDivisionPermissions($userId)
    {
        $builder = $this->db->table($this->table);
        $builder->select('divisions.code, divisions.name, user_divisions.is_head');
        $builder->join('divisions', 'divisions.id = user_divisions.division_id');
        $builder->where('user_divisions.user_id', $userId);
        
        return $builder->get()->getResultArray();
    }

    /**
     * Check if user can access division data
     */
    public function userCanAccessDivisionData($userId, $divisionCode)
    {
        $builder = $this->db->table($this->table);
        $builder->join('divisions', 'divisions.id = user_divisions.division_id');
        $builder->where('user_divisions.user_id', $userId);
        $builder->where('divisions.code', $divisionCode);
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Get user's division codes
     */
    public function getUserDivisionCodes($userId)
    {
        $builder = $this->db->table($this->table);
        $builder->select('divisions.code');
        $builder->join('divisions', 'divisions.id = user_divisions.division_id');
        $builder->where('user_divisions.user_id', $userId);
        
        $result = $builder->get()->getResultArray();
        return array_column($result, 'code');
    }

    /**
     * Add user to division (helper method for migration/setup)
     */
    public function addUserToDivision($userId, $divisionId, $isHead = false)
    {
        return $this->assignToDivision($userId, $divisionId, $isHead, session()->get('user_id'));
    }
}
