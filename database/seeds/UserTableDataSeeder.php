<?php

use Illuminate\Database\Seeder;

class UserTableDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->truncate();
        DB::table('users')->insert([
            'name' => 'Aspire customer',
            'email' => 'aspire-customer@gmail.com',
            'password' => Hash::make('Aspire@123')
        ]);
    }
}
