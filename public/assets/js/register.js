window.onload = async function () {
    const renterRadio = document.getElementById("check-renter");
    const landlordRadio = document.getElementById("check-landlord");
    const renterFields = document.getElementById("renter-fields");
    const landlordFields = document.getElementById("landlord-fields");
    const citySelect = document.getElementById("city");
    const regionSelect = document.getElementById("region");

    // Kiểm tra sự tồn tại của phần tử
    if (
        !renterRadio ||
        !landlordRadio ||
        !renterFields ||
        !landlordFields ||
        !citySelect ||
        !regionSelect
    ) {
        console.error("❌ Một hoặc nhiều phần tử không tồn tại trong DOM.");
        return;
    }

    // Hàm ẩn/hiện trường nhập liệu dựa trên vai trò
    function toggleFields() {
        const govId = document.querySelector('input[name="government_id"]');
        const proof = document.querySelector('input[name="proof"]');
        const city = document.querySelector('select[name="city"]');
        const region = document.querySelector('select[name="region"]');

        if (renterRadio.checked) {
            renterFields.style.display = "block";
            landlordFields.style.display = "none";

            if (govId) govId.value = "";
            if (proof) proof.value = "";
            if (govId) govId.removeAttribute("required");
            if (proof) proof.removeAttribute("required");

            if (city) city.setAttribute("required", true);
            if (region) region.setAttribute("required", true);
        } else if (landlordRadio.checked) {
            renterFields.style.display = "none";
            landlordFields.style.display = "block";

            if (city) city.value = "";
            if (region) region.value = "";
            if (city) city.removeAttribute("required");
            if (region) region.removeAttribute("required");

            if (govId) govId.setAttribute("required", true);
            if (proof) proof.setAttribute("required", true);
        } else {
            renterFields.style.display = "none";
            landlordFields.style.display = "none";
        }
    }

    // Lắng nghe thay đổi vai trò
    renterRadio.addEventListener("change", toggleFields);
    landlordRadio.addEventListener("change", toggleFields);

    // Lấy danh sách tỉnh/thành phố từ API
    async function fetchProvinces() {
        try {
            const response = await fetch(
                "https://provinces.open-api.vn/api/?depth=2"
            );
            return await response.json();
        } catch (error) {
            console.error("❌ Lỗi khi lấy dữ liệu từ API:", error);
            return [];
        }
    }

    // Cập nhật danh sách thành phố
    async function updateCities() {
        citySelect.innerHTML = "<option hidden>City</option>";
        regionSelect.innerHTML = "<option hidden>Region</option>";

        const provincesData = await fetchProvinces();
        if (provincesData.length > 0) {
            provincesData.forEach((province) => {
                const option = document.createElement("option");
                option.value = province.code;
                option.textContent = province.name;
                citySelect.appendChild(option);
            });
        } else {
            console.error("❌ Không có dữ liệu tỉnh/thành phố!");
        }
    }

    // Cập nhật danh sách quận/huyện khi chọn tỉnh/thành phố
    function updateRegions(selectedProvince) {
        regionSelect.innerHTML = "<option hidden>Region</option>";

        if (selectedProvince?.districts) {
            selectedProvince.districts.forEach((district) => {
                const option = document.createElement("option");
                option.value = district.code;
                option.textContent = district.name;
                regionSelect.appendChild(option);
            });
        }
    }

    // Xử lý thay đổi thành phố
    citySelect.addEventListener("change", function () {
        const selectedCode = this.value;
        fetchProvinces().then((provincesData) => {
            const selectedProvince = provincesData.find(
                (province) => province.code == selectedCode
            );
            if (selectedProvince) updateRegions(selectedProvince);
        });
    });

    // Khởi tạo danh sách thành phố
    await updateCities();

    // Xử lý đăng ký
    const registrationForm = document.querySelector("form");
    if (registrationForm) {
        // Thêm sự kiện submit để vô hiệu hóa nút đăng ký và thay đổi văn bản
        registrationForm.addEventListener("submit", function (event) {
            event.preventDefault(); // Ngăn form gửi mặc định

            // Vô hiệu hóa nút đăng ký và thay đổi văn bản
            const submitButton = document.getElementById("submitButton");
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerText = "Đang đăng ký...";
            }

            const role = document.querySelector(
                'input[name="role"]:checked'
            )?.value;
            if (!role) {
                alert("Vui lòng chọn vai trò (Renter hoặc Landlord).");
                if (submitButton) {
                    submitButton.disabled = false; // Kích hoạt lại nút nếu có lỗi
                    submitButton.innerText = "Submit";
                }
                return;
            }

            // Kiểm tra dữ liệu cho Renter
            if (role === "renter") {
                const city = document.querySelector(
                    'select[name="city"]'
                )?.value;
                const region = document.querySelector(
                    'select[name="region"]'
                )?.value;
                if (!city || !region) {
                    alert("Vui lòng chọn thành phố và khu vực.");
                    if (submitButton) {
                        submitButton.disabled = false; // Kích hoạt lại nút nếu có lỗi
                        submitButton.innerText = "Submit";
                    }
                    return;
                }
            }

            // Kiểm tra dữ liệu cho Landlord
            if (role === "landlord") {
                const governmentId = document.querySelector(
                    'input[name="government_id"]'
                )?.value;
                const proof = document.querySelector('input[name="proof"]')
                    ?.files[0];

                if (!governmentId) {
                    alert("Vui lòng nhập số CMND/CCCD.");
                    if (submitButton) {
                        submitButton.disabled = false; // Kích hoạt lại nút nếu có lỗi
                        submitButton.innerText = "Submit";
                    }
                    return;
                }
                if (!proof) {
                    alert("Vui lòng tải lên hình ảnh chứng minh.");
                    if (submitButton) {
                        submitButton.disabled = false; // Kích hoạt lại nút nếu có lỗi
                        submitButton.innerText = "Submit";
                    }
                    return;
                }
            }

            // Gửi dữ liệu bằng Fetch API
            const formData = new FormData(registrationForm);
            const csrfToken = document.querySelector(
                'meta[name="csrf-token"]'
            ).content;

            fetch(registrationForm.action, {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
            })
                .then(async (response) => {
                    // Kiểm tra Content-Type của phản hồi
                    const contentType = response.headers.get("content-type");
                    if (
                        !contentType ||
                        !contentType.includes("application/json")
                    ) {
                        // Nếu server trả về HTML, chuyển hướng ngay lập tức
                        window.location.href = "/email/verify";
                        return;
                    }
                    return await response.json();
                })
                .then((data) => {
                    if (data.success) {
                        alert("Đăng ký thành công! Vui lòng kiểm tra email.");
                        window.location.href = "/email/verify";
                    } else {
                        alert("Đăng ký thất bại: " + data.message);
                        const submitButton =
                            document.getElementById("submitButton");
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.innerText = "Submit";
                        }
                    }
                })
                .catch((error) => {
                    console.error("❌ Lỗi khi gửi dữ liệu:", error);
                    alert(error.message || "Đã xảy ra lỗi khi đăng ký.");
                    const submitButton =
                        document.getElementById("submitButton");
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.innerText = "Submit";
                    }
                });
        });
    }
};

// window.onload = async function () {
//     const renterRadio = document.getElementById("check-renter");
//     const landlordRadio = document.getElementById("check-landlord");
//     const renterFields = document.getElementById("renter-fields");
//     const landlordFields = document.getElementById("landlord-fields");
//     const citySelect = document.getElementById("city");
//     const regionSelect = document.getElementById("region");

//     // Kiểm tra sự tồn tại của phần tử
//     if (
//         !renterRadio ||
//         !landlordRadio ||
//         !renterFields ||
//         !landlordFields ||
//         !citySelect ||
//         !regionSelect
//     ) {
//         console.error("❌ Một hoặc nhiều phần tử không tồn tại trong DOM.");
//         return;
//     }

//     // Hàm ẩn/hiện trường nhập liệu dựa trên vai trò
//     function toggleFields() {
//         const govId = document.querySelector('input[name="government_id"]');
//         const proof = document.querySelector('input[name="proof"]');
//         const city = document.querySelector('select[name="city"]');
//         const region = document.querySelector('select[name="region"]');

//         if (renterRadio.checked) {
//             renterFields.style.display = "block";
//             landlordFields.style.display = "none";

//             if (govId) govId.value = "";
//             if (proof) proof.value = "";
//             if (govId) govId.removeAttribute("required");
//             if (proof) proof.removeAttribute("required");

//             if (city) city.setAttribute("required", true);
//             if (region) region.setAttribute("required", true);
//         } else if (landlordRadio.checked) {
//             renterFields.style.display = "none";
//             landlordFields.style.display = "block";

//             if (city) city.value = "";
//             if (region) region.value = "";
//             if (city) city.removeAttribute("required");
//             if (region) region.removeAttribute("required");

//             if (govId) govId.setAttribute("required", true);
//             if (proof) proof.setAttribute("required", true);
//         } else {
//             renterFields.style.display = "none";
//             landlordFields.style.display = "none";
//         }
//     }

//     // Lắng nghe thay đổi vai trò
//     renterRadio.addEventListener("change", toggleFields);
//     landlordRadio.addEventListener("change", toggleFields);

//     // Lấy danh sách tỉnh/thành phố từ API
//     async function fetchProvinces() {
//         try {
//             const response = await fetch(
//                 "https://provinces.open-api.vn/api/?depth=2"
//             );
//             return await response.json();
//         } catch (error) {
//             console.error("❌ Lỗi khi lấy dữ liệu từ API:", error);
//             return [];
//         }
//     }

//     // Cập nhật danh sách thành phố
//     async function updateCities() {
//         citySelect.innerHTML = "<option hidden>City</option>";
//         regionSelect.innerHTML = "<option hidden>Region</option>";

//         const provincesData = await fetchProvinces();
//         if (provincesData.length > 0) {
//             provincesData.forEach((province) => {
//                 const option = document.createElement("option");
//                 option.value = province.code;
//                 option.textContent = province.name;
//                 citySelect.appendChild(option);
//             });
//         } else {
//             console.error("❌ Không có dữ liệu tỉnh/thành phố!");
//         }
//     }

//     // Cập nhật danh sách quận/huyện khi chọn tỉnh/thành phố
//     function updateRegions(selectedProvince) {
//         regionSelect.innerHTML = "<option hidden>Region</option>";

//         if (selectedProvince?.districts) {
//             selectedProvince.districts.forEach((district) => {
//                 const option = document.createElement("option");
//                 option.value = district.code;
//                 option.textContent = district.name;
//                 regionSelect.appendChild(option);
//             });
//         }
//     }

//     // Xử lý thay đổi thành phố
//     citySelect.addEventListener("change", function () {
//         const selectedCode = this.value;
//         fetchProvinces().then((provincesData) => {
//             const selectedProvince = provincesData.find(
//                 (province) => province.code == selectedCode
//             );
//             if (selectedProvince) updateRegions(selectedProvince);
//         });
//     });

//     // Khởi tạo danh sách thành phố
//     await updateCities();

//     // Xử lý đăng ký
//     const registrationForm = document.querySelector("form");
//     if (registrationForm) {
//         registrationForm.addEventListener("submit", function (event) {
//             event.preventDefault(); // Ngăn form gửi mặc định

//             const role = document.querySelector(
//                 'input[name="role"]:checked'
//             )?.value;
//             if (!role) {
//                 alert("Vui lòng chọn vai trò (Renter hoặc Landlord).");
//                 return;
//             }

//             // Kiểm tra dữ liệu cho Renter
//             if (role === "renter") {
//                 const city = document.querySelector(
//                     'select[name="city"]'
//                 )?.value;
//                 const region = document.querySelector(
//                     'select[name="region"]'
//                 )?.value;
//                 if (!city || !region) {
//                     alert("Vui lòng chọn thành phố và khu vực.");
//                     return;
//                 }
//             }

//             // Kiểm tra dữ liệu cho Landlord
//             if (role === "landlord") {
//                 const governmentId = document.querySelector(
//                     'input[name="government_id"]'
//                 )?.value;
//                 const proof = document.querySelector('input[name="proof"]')
//                     ?.files[0];

//                 if (!governmentId) {
//                     alert("Vui lòng nhập số CMND/CCCD.");
//                     return;
//                 }
//                 if (!proof) {
//                     alert("Vui lòng tải lên hình ảnh chứng minh.");
//                     return;
//                 }
//             }

//             // Gửi dữ liệu bằng Fetch API
//             const formData = new FormData(registrationForm);
//             const csrfToken = document.querySelector(
//                 'meta[name="csrf-token"]'
//             ).content;

//             fetch(registrationForm.action, {
//                 method: "POST",
//                 body: formData,
//                 headers: {
//                     "X-CSRF-TOKEN": csrfToken,
//                     Accept: "application/json",
//                 },
//             })
//                 .then(async (response) => {
//                     // Nếu server trả về HTML, báo lỗi thay vì parse JSON
//                     const contentType = response.headers.get("content-type");
//                     if (
//                         !contentType ||
//                         !contentType.includes("application/json")
//                     ) {
//                         throw new Error(
//                             "Server trả về HTML thay vì JSON. Vui lòng kiểm tra email xác nhận."
//                         );
//                     }
//                     return await response.json();
//                 })
//                 .then((data) => {
//                     if (data.success) {
//                         alert("Đăng ký thành công! Vui lòng kiểm tra email.");
//                         window.location.href = "/email/verify";
//                     } else {
//                         alert("Đăng ký thất bại: " + data.message);
//                     }
//                 })
//                 .catch((error) => {
//                     console.error("❌ Lỗi khi gửi dữ liệu:", error);
//                     alert(error.message || "Đã xảy ra lỗi khi đăng ký.");
//                 });
//         });
//     }
// };
