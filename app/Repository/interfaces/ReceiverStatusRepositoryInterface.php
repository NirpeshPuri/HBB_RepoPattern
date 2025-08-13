<?php

namespace App\Repository\interfaces;

use Illuminate\Http\Request;

interface ReceiverStatusRepositoryInterface
{
    public function index();
    public function edit($id);
    public function update(Request $request, $id);
    public function destroy($id);
}
