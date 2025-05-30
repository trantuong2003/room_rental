@extends('layouts.admin')

@section('content')
<div class="admin-container">
    <div class="head-title">
        <div class="left">
            <h1>Manage banned keywords</h1>
            <ul class="breadcrumb">
                <li>
                    <a href="#">Control Panel</a>
                </li>
                <li><i class='bx bx-chevron-right'></i></li>
                <li>
                    <a class="active" href="#">Banned keywords</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="banned-words-container">
        <div class="banned-words-header">
            <h2>List of banned keywords</h2>
            <button class="add-word-btn" id="addWordBtn">
                <i class='bx bx-plus'></i> Add new word
            </button>
        </div>

        <div class="search-section">
            <div class="search-group">
                <input type="text" id="searchWord" placeholder="Search for keywords...">
                <button type="button"><i class='bx bx-search'></i></button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="banned-words-table">
                <thead>
                    <tr>
                        <th class="order-column">STT</th>
                        <th>Keywords</th>
                        <th>Date added</th>
                        <th>Operation</th>
                    </tr>
                </thead>
                <tbody id="bannedWordsTableBody">
                    <tr>
                        <td colspan="4" class="text-center">Loading data...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="pagination" id="pagination"></div>
    </div>

    <!-- Modal thêm/sửa từ khóa -->
    <div id="wordModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add banned keywords</h3>
                <span class="close-modal">&times;</span>
            </div>
            <form id="wordForm">
                <input type="hidden" id="wordId">
                <div class="form-group">
                    <label for="word">Keywords:</label>
                    <input type="text" id="word" required>
                </div>
                <div class="form-actions">
                    <button type="button" class="cancel-btn" id="cancelBtn">Cancel</button>
                    <button type="submit" class="save-btn">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal xác nhận xóa -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Confirm deletion</h3>
                <span class="close-modal">&times;</span>
            </div>
            <p>Are you sure you want to delete this keyword?</p>
            <div class="form-actions">
                <button type="button" class="cancel-btn" id="cancelDeleteBtn">Cancel</button>
                <button type="button" class="delete-btn" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection


<script>
    document.addEventListener('DOMContentLoaded', function () {
    // Khai báo các biến DOM
    const addWordBtn = document.getElementById('addWordBtn');
    const wordModal = document.getElementById('wordModal');
    const deleteModal = document.getElementById('deleteModal');
    const wordForm = document.getElementById('wordForm');
    const modalTitle = document.getElementById('modalTitle');
    const wordInput = document.getElementById('word');
    const wordIdInput = document.getElementById('wordId');
    const cancelBtn = document.getElementById('cancelBtn');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    const searchWord = document.getElementById('searchWord');
    const bannedWordsTableBody = document.getElementById('bannedWordsTableBody');
    const pagination = document.getElementById('pagination');

    let currentPage = 1;
    let perPage = 10;
    let deleteWordId = null;

    // Hàm mở modal
    function openModal(modal) {
        modal.style.display = 'block';
    }

    // Hàm đóng modal
    function closeModal(modal) {
        modal.style.display = 'none';
    }

    // Đóng modal khi nhấn nút đóng
    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            closeModal(wordModal);
            closeModal(deleteModal);
        });
    });

    // Đóng modal khi nhấn bên ngoài
    window.addEventListener('click', (event) => {
        if (event.target === wordModal) {
            closeModal(wordModal);
        }
        if (event.target === deleteModal) {
            closeModal(deleteModal);
        }
    });

    // Hàm lấy danh sách từ khóa
    function fetchBannedWords(page = 1, search = '') {
        fetch(`/admin/api/banned-words?page=${page}&per_page=${perPage}&search=${encodeURIComponent(search)}`)
            .then(response => response.json())
            .then(data => {
                renderTable(data.data);
                renderPagination(data);
            })
            .catch(error => {
                console.error('Error getting keyword list:', error);
                bannedWordsTableBody.innerHTML = '<tr><td colspan="4" class="text-center">An error occurred while loading data.</td></tr>';
            });
    }

    // Hàm hiển thị bảng từ khóa
    function renderTable(words) {
        bannedWordsTableBody.innerHTML = '';
        if (words.length === 0) {
            bannedWordsTableBody.innerHTML = '<tr><td colspan="4" class="text-center">Keyword not found</td></tr>';
            return;
        }

        words.forEach((word, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="order-column">${index + 1 + (currentPage - 1) * perPage}</td>
                <td>${word.word}</td>
                <td>${new Date(word.created_at).toLocaleDateString('vi-VN')}</td>
                <td>
                    <div class="action-buttons">
                        <button class="edit-btn" data-id="${word.id}" data-word="${word.word}">
                            <i class='bx bx-edit'></i>
                        </button>
                        <button class="delete-btn" data-id="${word.id}">
                            <i class='bx bx-trash'></i>
                        </button>
                    </div>
                </td>
            `;
            bannedWordsTableBody.appendChild(row);
        });

        // Gán sự kiện cho nút chỉnh sửa và xóa
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                const word = btn.getAttribute('data-word');
                openEditModal(id, word);
            });
        });

        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                deleteWordId = btn.getAttribute('data-id');
                openModal(deleteModal);
            });
        });
    }

    // Hàm hiển thị phân trang
    function renderPagination(data) {
        pagination.innerHTML = '';
        const totalPages = data.last_page;

        if (totalPages <= 1) return;

        for (let i = 1; i <= totalPages; i++) {
            const button = document.createElement('button');
            button.textContent = i;
            if (i === data.current_page) {
                button.classList.add('active');
            }
            button.addEventListener('click', () => {
                currentPage = i;
                fetchBannedWords(currentPage, searchWord.value);
            });
            pagination.appendChild(button);
        }
    }

    // Hàm mở modal chỉnh sửa
    function openEditModal(id, word) {
        modalTitle.textContent = 'Edit banned keywords';
        wordInput.value = word;
        wordIdInput.value = id;
        openModal(wordModal);
    }

    // Hàm mở modal thêm từ mới
    addWordBtn.addEventListener('click', () => {
        modalTitle.textContent = 'Add banned keywords';
        wordForm.reset();
        wordIdInput.value = '';
        openModal(wordModal);
    });

    // Hủy modal
    cancelBtn.addEventListener('click', () => {
        closeModal(wordModal);
    });

    cancelDeleteBtn.addEventListener('click', () => {
        closeModal(deleteModal);
    });

    // Xử lý submit form thêm/sửa
    wordForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const id = wordIdInput.value;
        const word = wordInput.value.trim();

        const url = id ? `/admin/api/banned-words/${id}` : '/admin/api/banned-words';
        const method = id ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ word: word })
        })
            .then(response => response.json())
            .then(data => {
                closeModal(wordModal);
                fetchBannedWords(currentPage, searchWord.value);
                alert(data.message);
            })
            .catch(error => {
                console.error('error:', error);
                alert('An error occurred while saving the keyword.');
            });
    });

    // Xử lý xóa từ khóa
    confirmDeleteBtn.addEventListener('click', () => {
        fetch(`/admin/api/banned-words/${deleteWordId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(response => response.json())
            .then(data => {
                closeModal(deleteModal);
                fetchBannedWords(currentPage, searchWord.value);
                alert(data.message);
            })
            .catch(error => {
                console.error('error', error);
                alert('An error occurred while deleting the keyword.');
            });
    });

    // Xử lý tìm kiếm
    searchWord.addEventListener('input', () => {
        currentPage = 1;
        fetchBannedWords(currentPage, searchWord.value);
    });

    // Tải danh sách từ khóa khi trang được tải
    fetchBannedWords();
});
</script>