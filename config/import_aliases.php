<?php

return [
    // Alias mapping for MPPD import headers.
    // Keys are normalized (lowercase, punctuation removed, spaces collapsed).
    'mppd' => [
        // Nomor Register
        'no' => 'nomor_register',
        'nomor' => 'nomor_register',
        'register' => 'nomor_register',
        'no register' => 'nomor_register',
        'nomor register' => 'nomor_register',
        // NIK & Nama
        'nik' => 'nik',
        'nama' => 'nama',
        'alamat' => 'alamat',
        'email' => 'email',
        // Telepon
        'nomor telepon' => 'nomor_telp',
        'no telepon' => 'nomor_telp',
        'telepon' => 'nomor_telp',
        'no telp' => 'nomor_telp',
        'telp' => 'nomor_telp',
        'nomor telp' => 'nomor_telp',
        // STR
        'nomor_str' => 'nomor_str',
        'nomor str' => 'nomor_str',
        'str' => 'nomor_str',
        'masa berlaku str' => 'masa_berlaku_str',
        'expired str' => 'masa_berlaku_str',
        'masa str' => 'masa_berlaku_str',
        'masa_berlaku_str' => 'masa_berlaku_str',
        // Profesi & Praktik
        'profesi' => 'profesi',
        'tempat praktik' => 'tempat_praktik',
        'tempat praktek' => 'tempat_praktik',
        'tempat_praktik' => 'tempat_praktik',
        'alamat tempat praktik' => 'alamat_tempat_praktik',
        'alamat tempat praktek' => 'alamat_tempat_praktik',
        'alamat_tempat_praktik' => 'alamat_tempat_praktik',
        // SIP
        'nomor sip' => 'nomor_sip',
        'no sip' => 'nomor_sip',
        'sip' => 'nomor_sip',
        'nomor_sip' => 'nomor_sip',
        'tanggal sip' => 'tanggal_sip',
        'tgl sip' => 'tanggal_sip',
        'tgl sip terbit' => 'tanggal_sip',
        'tanggal_terbit_sip' => 'tanggal_sip',
        'tanggal akhir sip' => 'tanggal_akhir_sip',
        'tgl akhir sip' => 'tanggal_akhir_sip',
        'tanggal_akhir_sip' => 'tanggal_akhir_sip',
        // Keterangan
        'keterangan' => 'keterangan',
    ],
];
