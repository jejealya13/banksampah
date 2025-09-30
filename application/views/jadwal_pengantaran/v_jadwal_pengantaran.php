<a href="<?= base_url('jadwal_pengantaran/add') ?>" style="background-color: #28a745; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; display: inline-block; margin-bottom: 10px;">
    <i class="fa fa-plus"></i> Tambah Jadwal Pengantaran
</a>

<table id="jadwalTable" class="table table-bordered" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th style="padding: 10px; border: 1px solid #ddd;">Deskripsi</th>
            <th style="padding: 10px; border: 1px solid #ddd; width: 100px; text-align: center;">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($jadwal as $row): ?>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd; vertical-align: middle;"><?= htmlspecialchars($row->description) ?></td>
            <td style="padding: 10px; border: 1px solid #ddd; text-align: center; vertical-align: middle;">
                <a href="<?= base_url('jadwal_pengantaran/edit/'.$row->id) ?>" style="background-color: #ffc107; border: none; padding: 5px 10px; color: #212529; border-radius: 4px; text-decoration: none; display: inline-block;" title="Edit">
                    <i class="fa fa-pencil"></i>
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

