<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

abstract class MasterController extends Controller
{
    abstract protected function model(): string;
    abstract protected function judul(): string;
    abstract protected function prefix(): string;
    abstract protected function fields(): array;
    abstract protected function rules(?int $id = null): array;

    protected function viewData(): array
    {
        return [
            'judul'  => $this->judul(),
            'prefix' => $this->prefix(),
            'fields' => $this->fields(),
        ];
    }

    public function index()
    {
        $model = $this->model();
        $items = $model::orderBy('nama')->get();

        return view('admin.master.index', $this->viewData() + compact('items'));
    }

    public function store(Request $request)
    {
        $model = $this->model();
        $model::create($request->validate($this->rules()));

        return redirect()->route($this->prefix() . '.index')
            ->with('success', $this->judul() . ' berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $model = $this->model();
        $item = $model::findOrFail($id);

        return view('admin.master.edit', $this->viewData() + compact('item'));
    }

    public function update(Request $request, string $id)
    {
        $model = $this->model();
        $item = $model::findOrFail($id);
        $item->update($request->validate($this->rules((int) $id)));

        return redirect()->route($this->prefix() . '.index')
            ->with('success', $this->judul() . ' berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $model = $this->model();
        $item = $model::findOrFail($id);

        try {
            $item->delete();
        } catch (QueryException $e) {
            return back()->with('error', $this->judul() . ' tidak bisa dihapus karena masih dipakai oleh data lain.');
        }

        return back()->with('success', $this->judul() . ' berhasil dihapus.');
    }
}