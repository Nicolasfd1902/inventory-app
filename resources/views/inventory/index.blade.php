<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Manajemen Inventaris</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light p-4">
    <div class="container">
        <h2 class="mb-4">Aplikasi Inventaris Sederhana</h2>

        <!-- Input Barang Baru -->
        <div class="card mb-4">
            <div class="card-header">Tambah Barang Baru</div>
            <div class="card-body">
                <form id="form-item">
                    <div class="row">
                        <div class="col-md-2">
                            <input type="text" id="code" class="form-control" placeholder="Kode Barang"
                                required>
                        </div>
                        <div class="col-md-3">
                            <input type="text" id="name" class="form-control" placeholder="Nama Barang"
                                required>
                        </div>
                        <div class="col-md-2">
                            <select id="category_id" class="form-select" required>
                                <option value="">Pilih Kategori</option>
                                @foreach (\App\Models\Category::all() as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" id="stock" class="form-control" placeholder="Stok Awal"
                                min="0" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" id="price" class="form-control" placeholder="Harga"
                                min="0" required>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Daftar Barang & Pencarian -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Daftar Barang</span>
                <input type="text" id="search" class="form-control w-25" placeholder="Cari nama/kode barang..."
                    onkeyup="fetchItems()">
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th>Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table-items"></tbody>
                </table>
            </div>
        </div>

        <!-- Riwayat Stok -->
        <div class="card">
            <div class="card-header">Riwayat Stok (Mutasi Masuk / Keluar)</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Barang</th>
                            <th>Tipe</th>
                            <th>Jumlah</th>
                            <th>Alasan</th>
                        </tr>
                    </thead>
                    <tbody id="table-logs"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Edit Barang -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-edit-item">
                    <div class="modal-body">
                        <input type="hidden" id="edit-id">
                        <div class="mb-3">
                            <label class="form-label">Kode Barang</label>
                            <input type="text" id="edit-code" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" id="edit-name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select id="edit-category_id" class="form-select" required>
                                @foreach (\App\Models\Category::all() as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga (>=0)</label>
                            <input type="number" id="edit-price" class="form-control" min="0" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Fetch Data Barang (Pencarian Tanpa Reload)
        async function fetchItems() {
            const query = document.getElementById('search').value;
            const res = await fetch(`/items/data?search=${query}`);
            const data = await res.json();

            let rows = '';
            data.forEach(item => {
                rows += `
                    <tr>
                        <td>${item.code}</td>
                        <td>${item.name}</td>
                        <td>${item.category ? item.category.name : '-'}</td>
                        <td><strong>${item.stock}</strong></td>
                        <td>Rp ${Number(item.price).toLocaleString('id-ID')}</td>
                        <td>
                            <button class="btn btn-sm btn-success" onclick="transaksiStok(${item.id}, 'masuk')"> Masuk</button>
                            <button class="btn btn-sm btn-danger" onclick="transaksiStok(${item.id}, 'keluar')"> Keluar</button>
                            <button class="btn btn-sm btn-warning me-1" onclick="openEditModal(${item.id}, '${item.code}', '${item.name}', ${item.category_id}, ${item.price})">Edit</button>
                        </td>
                    </tr>
                `;
            });
            document.getElementById('table-items').innerHTML = rows;
        }

        // Buka Modal Edit Barang
        function openEditModal(id, code, name, category_id, price) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-code').value = code;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-category_id').value = category_id;
            document.getElementById('edit-price').value = price;

            const modalElement = document.getElementById('modalEdit');
            let modalInstance = bootstrap.Modal.getInstance(modalElement);

            if (!modalInstance) {
                modalInstance = new bootstrap.Modal(modalElement);
            }

            modalInstance.show();
        }

        // Submit Form Edit Barang lewat Fetch API
        // Submit Form Edit Barang lewat Fetch API
        document.getElementById('form-edit-item').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('edit-id').value;
            const payload = {
                code: document.getElementById('edit-code').value,
                name: document.getElementById('edit-name').value,
                category_id: document.getElementById('edit-category_id').value,
                price: document.getElementById('edit-price').value,
            };

            const res = await fetch(`/items/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const result = await res.json();

            if (res.ok) {
                // Sembunyikan modal dulu baru munculkan alert sekali
                const modalElement = document.getElementById('modalEdit');
                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                if (modalInstance) {
                    modalInstance.hide();
                }

                alert(result.message || 'Data barang berhasil diperbarui!');
                fetchItems();
            } else {
                let errorMsg = result.message;
                if (result.errors) {
                    errorMsg = Object.values(result.errors).flat().join('\n');
                }
                alert('Gagal mengedit:\n' + errorMsg);
            }
        });

        // Submit Form Barang Baru lewat Fetch API
        document.getElementById('form-item').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {
                code: document.getElementById('code').value,
                name: document.getElementById('name').value,
                category_id: document.getElementById('category_id').value,
                stock: document.getElementById('stock').value,
                price: document.getElementById('price').value,
            };

            const res = await fetch('/items', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const result = await res.json();
            if (res.ok) {
                alert('Barang berhasil ditambahkan!');
                document.getElementById('form-item').reset();
                fetchItems();
            } else {
                let errorMsg = result.message;
                if (result.errors) {
                    errorMsg = Object.values(result.errors).flat().join('\n');
                }
                alert('Gagal menyimpan:\n' + errorMsg);
            }
        });

        // Transaksi Stok Masuk / Keluar
        async function transaksiStok(itemId, type) {
            const qty = prompt(`Masukkan jumlah stok ${type}:`);
            if (!qty || qty <= 0) return;

            const reason = prompt(`Masukkan alasan transaksi stok ${type}:`);
            if (!reason) return;

            const res = await fetch('/stock-mutations', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    item_id: itemId,
                    type: type,
                    qty: qty,
                    reason: reason
                })
            });

            const result = await res.json();
            alert(result.message);
            if (res.ok) {
                fetchItems();
                fetchLogs();
            }
        }

        // Fetch Log Mutasi Stok
        async function fetchLogs() {
            const res = await fetch('/stock-mutations/data');
            const data = await res.json();
            let rows = '';
            data.forEach(log => {
                rows += `
                    <tr>
                        <td>${new Date(log.created_at).toLocaleString('id-ID')}</td>
                        <td>${log.item ? log.item.name : '-'} (${log.item ? log.item.code : '-'})</td>
                        <td><span class="badge bg-${log.type === 'masuk' ? 'success' : 'danger'}">${log.type.toUpperCase()}</span></td>
                        <td>${log.qty}</td>
                        <td>${log.reason}</td>
                    </tr>
                `;
            });
            document.getElementById('table-logs').innerHTML = rows;
        }

        // Initial Load
        fetchItems();
        fetchLogs();
    </script>
</body>

</html>
