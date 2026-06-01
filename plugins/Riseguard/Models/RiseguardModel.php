<?php
namespace Riseguard\Models;

use App\Models\Crud_model; //access main app's models

class RiseguardModel extends Crud_model {

	protected $table = null;

	public function __construct() {
		$this->table = 'riseguard_logs';
		parent::__construct($this->table);
	}

	public function getAllLogs()
	{
		$query = $this->db->table($this->table)
        ->get();

		return $query->getResultArray();
	}

	public function getBlackListData($type)
	{
		$query = $this->db->table('blacklist')->where(['type' => $type])
        ->get();

		return $query->getResultArray();
	}

	public function getAllStaff()
	{
		$query = $this->db->table('users')->where(['user_type' => 'staff', 'is_admin' => '0'])
        ->get();

		return $query->getResultArray();
	}

	public function addStaffExpiry($postData)
    {
    	$update = $postData['update'];
    	unset($postData['update']);
        $postData['expiry_date'] = $postData['expiry_date'];
        $id = $postData['staffid'];
        unset($postData['staffid']);
		$res = $this->db->table('users')->where(['id'=> $id])->update($postData);

        return [
            'success' => $res ? 'true' : 'false',
            'message' => ($update && $res) ? app_lang('expiry_date_updated_successfully') : ($res ? app_lang('expiry_date_added') : app_lang('something_went_wrong'))
        ];
    }

    public function getStaffExpiry()
    {
		$query = $this->db->table('users')
        ->where('user_type', 'staff')
        ->where('is_admin', '0')
        ->where('expiry_date !=', null) // Use '!=' to represent "not equal" condition
        ->get();

        return $query->getResultArray();
    }

    public function isUniqueIpOrEmail($ipEmail, $type)
    {
    	$query = $this->db->table('blacklist')
		    ->whereIn('ip_email', $ipEmail)
		    ->where('type', $type)
		    ->get();

		$blacklistData = $query->getResultArray();
        

        if (!empty($blacklistData)) {
            return [
                'success' => false,
                'type' =>  $type,
                'message' => ($type == 'ip') ? app_lang('ip_address_already_exists') : app_lang('email_address_already_exists'),
                'ipEmail' => array_column($blacklistData, 'ip_email')
            ];
        }

        return [
            'success' => true,
            'type' =>  $type,
            'message' => ''
        ];
    }

     /**
     * Add a batch of IP addresses or Email Addresses to the blacklist.
     *
     * @param array $postData The array of data to be inserted.
     * @return array Returns an array containing the type of operation ('success' or 'danger') and a message indicating the status of the operation.
     */
	public function addIpOrEmailToBlacklist($postData)
	{
	    // Insert the data into the database
	    $insert = $this->db->table('blacklist')->insertBatch($postData);

	    // Set the appropriate message based on the type of data being blacklisted
	    $message = ($postData[0]['type'] == 'ip') ? app_lang('ip_added_to_blacklist') : app_lang('email_added_to_blacklist');

	    // Return an array with the operation type and status message
	    return [
	    	'status'  => $insert,
	        'type'    => $postData[0]['type'] ?? '',
	        'message' => $insert ? $message : app_lang('something_went_wrong')
	    ];
	}

	public function removeStaffExpiry($staffID)
    {
    	return $this->db->table('users')->where(['id'=> $staffID])->update(['expiry_date' => NULL]);
    }

    /**
     * Removes a IP or Email from the blacklist.
     *
     * @param string $category ('ip' or 'email')
     * @return int $id ID of the record to be deleted.
     */
    public function removeIpOrEmailFromBlacklist($category, $id)
    {
    	return $this->db->table('blacklist')->delete(['id'=> $id]);
    }

    public function riseguardClearLog()
    {
        return $this->db->table('riseguard_logs')->truncate();
    }


}
