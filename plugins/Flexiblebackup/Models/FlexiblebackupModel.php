<?php

namespace Flexiblebackup\Models;

use App\Models\Crud_model;

class FlexiblebackupModel extends Crud_model
{
    protected $table = 'flexiblebackup';

    public function __construct()
    {
        parent::__construct($this->table);
    }

    /**
     * Get a backup by ID.
     *
     * @param int $id the ID of the backup to retrieve
     *
     * @return object|null the backup object or null if not found
     */
    public function getBackupById($id)
    {
        $query = $this->db->table($this->table)->where('id', $id)->get();

        return $query->getRow();
    }

    /**
     * Get all backups.
     *
     * @return array an array of backup records
     */
    public function getAllBackups()
    {
        $query = $this->db->table($this->table)->get();

        return $query->getResultArray();
    }

    /**
     * Add a new backup.
     *
     * @param array $data an array of backup data
     *
     * @return int the ID of the newly inserted backup
     */
    public function storeBackup(array $data)
    {
        $this->db->table($this->table)->insert([
            'backup_name' => $data['name'],
            'backup_type' => $data['type'],
            'backup_data' => '',
            'datecreated' => date('Y-m-d H:i:s'),
        ]);

        return $this->db->insertID();
    }

    /**
     * Update a backup with additional data.
     *
     * @param int    $id      the ID of the backup to update
     * @param string $newData the new data to add to the backup
     *
     * @return int the number of affected rows after the update
     */
    public function updateBackupData($newData, $id)
    {
        $backup       = $this->getBackupById($id);
        $existingData = $backup->backup_data;
        $newDataValue = $newData['key'];
        $newValue     = ('' == $existingData) ? $newDataValue : $existingData.','.$newDataValue;

        return $this->db->table($this->table)->where('id', $id)->update(['backup_data' => $newValue]);
    }

    /**
     * Mark a backup as uploaded to remote storage.
     *
     * @param int $id the ID of the backup
     *
     * @return int the number of affected rows after the update
     */
    public function markBackupAsUploadedToRemote($id)
    {
        return $this->db->table($this->table)->where('id', $id)->update(['uploaded_to_remote' => 1]);
    }

    /**
     * Delete a backup folder by ID.
     *
     * @param int $id the ID of the backup
     *
     * @return int the number of deleted rows
     */
    public function deleteBackupFolder($id)
    {
        return $this->db->table($this->table)->where('id', $id)->delete();
    }

    /**
     * Remove a specific data item from a backup's data list.
     *
     * @param object $backup  the backup object
     * @param string $dataKey the data item to remove
     *
     * @return int the number of affected rows after the update or the number of deleted rows if the data is empty or of database type
     */
    public function removeBackupData($backup, $dataKey)
    {
        $id                = $backup->id;
        $existingData      = $backup->backup_data;
        $existingDataArray = explode(',', $existingData);
        $newDataArray      = [];

        foreach ($existingDataArray as $existingDataItem) {
            if ($existingDataItem !== $dataKey) {
                $newDataArray[] = $existingDataItem;
            }
        }

        $newDataValue = implode(',', $newDataArray);

        if ('database' === $dataKey || empty($newDataArray)) {
            return $this->deleteBackupFolder($id);
        }

        return $this->db->table($this->table)->where('id', $id)->update(['backup_data' => $newDataValue]);
    }
}
