# ATTG Contact List System

ระบบสมุดรายชื่อและข้อมูลติดต่อบุคลากร (Contact List Directory) สำหรับกลุ่มบริษัท ATTG และเครือข่ายบริษัท โดยรองรับการค้นหาข้อมูลติดต่อ, การแสดงแผนผังโครงสร้างสายงาน (Function / Department / Section), ระบบจัดการข้อมูลสำหรับผู้ดูแลระบบ (Admin CRUD) และระบบจัดการโปรไฟล์พนักงานด้วยตนเองผ่าน LDAP

---

## 🌟 ฟีเจอร์หลัก (Key Features)

### 1. หน้าค้นหาและแสดงข้อมูลบุคลากร (Public Contact Directory)
- **Live Search & Autocomplete**: ค้นหาข้อมูลพนักงานได้แบบเรียลไทม์ (ชื่อภาษาไทย, ชื่อภาษาอังกฤษ, รหัสพนักงาน, เบอร์มือถือ, เบอร์ภายใน, อีเมล) พร้อมระบบ Highlight คำค้นหา
- **ตัวกรองตามบริษัทและสายงาน (Company & Function Filter)**: เลือกดูข้อมูลพนักงานแยกตามบริษัทและหน่วยงาน หรือเลือกดูแบบรวมทั้งหมด
- **โครงสร้างสายงานอัตโนมัติ**: แสดงการจัดกลุ่มตาม Function → Department → Section โดยอ้างอิงลำดับตาม `OrganizeLevel` และ `OrganizeOrder`
- **รองรับการ Export ข้อมูล**: ส่งออกรายชื่อพนักงานและข้อมูลติดต่อเป็นไฟล์ Excel (.xlsx)

### 2. ระบบจัดการหลังบ้าน (Admin CRUD System)
- **Employee Management**: เพิ่ม ลบ แก้ไข ข้อมูลพนักงาน พร้อมอัปโหลดและจัดการรูปภาพโปรไฟล์
- **Organizational Structure Management**: จัดการข้อมูล Function, Department, Section ของแต่ละบริษัท
- **Position & Level Management**: จัดการตำแหน่ง, ระดับลำดับชั้น (OrganizeLevel / OrganizeOrder) และสถานะบอร์ดบริหาร
- **Admin Account Management**: จัดการบัญชีผู้ดูแลระบบ กำหนดสิทธิ์ Master Admin (ดูแลทุกบริษัท) หรือ Company Admin (เฉพาะบริษัทตนเอง)

### 3. ระบบโปรไฟล์พนักงาน (Employee Self-Service)
- **LDAP Authentication**: เข้าสู่ระบบด้วย Active Directory Domain Username/Password พนักงานขององค์กร
- **Profile Update**: พนักงานสามารถแก้ไขเบอร์โทรศัพท์มือถือที่ยินดีให้แสดงผล และเบอร์ต่อภายใน (Internal Extension) ได้ด้วยตนเอง

---

## 🏗️ โครงสร้างสถาปัตยกรรมโปรเจกต์ (Project Architecture)

โปรเจกต์ได้รับการปรับปรุงโครงสร้าง (Refactor) ตามแนวคิด **Clean MVC Architecture** และ **Component-Based Modularity**:

```text
ContactList/
├── application/
│   ├── config/             # การตั้งค่าระบบ (Database, Routes, Config)
│   ├── controllers/
│   │   └── send_data.php   # Controller หลัก แบ่ง 10 หมวดหมู่การทำงาน
│   ├── models/             # Encapsulated Business Logic & Database Queries
│   │   ├── AdminLogin_model.php
│   │   ├── Company_model.php
│   │   ├── Department_model.php
│   │   ├── Employee_model.php
│   │   ├── Function_model.php
│   │   ├── Map_model.php
│   │   ├── Position_model.php
│   │   └── Section_model.php
│   └── views/
│       ├── admin/
│       │   └── modals/     # Modals ฝั่ง Admin แยกเป็นโมดูลาร์ย่อย
│       │       ├── modal_admin.php
│       │       ├── modal_department.php
│       │       ├── modal_employee.php
│       │       ├── modal_function.php
│       │       ├── modal_map.php
│       │       ├── modal_position.php
│       │       └── modal_section.php
│       ├── partials/       # Shared Layouts (ใช้ร่วมกันทุกหน้า)
│       │   ├── header.php
│       │   └── footer.php
│       ├── home.php        # หน้าแรกแสดงรายชื่อ Contact List
│       ├── admin_crud.php  # หน้าจัดการข้อมูล Admin
│       └── employee_crud.php # หน้าโปรไฟล์พนักงาน
├── assets/                 # สไตล์ชีต สคริปต์ ไอคอน และรูปภาพพนักงาน
└── system/                 # CodeIgniter Framework Core
```

---

## 📝 บันทึกการเปลี่ยนแปลงและปรับปรุงโค้ด (Refactoring Changelog)

### 1. ส่วน Models (`application/models/`)
- **Encapsulate Direct Database Queries**: ย้ายการ Query ฐานข้อมูลโดยตรงที่เคยเขียนปะปนใน Controller ทั้งหมดเข้ามาเป็น Method ใน Model ที่เกี่ยวข้องอย่างเป็นระเบียบ
  - `Employee_model`: เพิ่ม `update_employee_profile_and_internal()`, `get_employee_picture_info()`, `get_active_employees_dropdown()`
  - `Department_model`: เพิ่ม `get_departments_by_function()`
  - `Section_model`: เพิ่ม `get_sections_by_department()`
- **ลบโค้ดซ้ำซ้อน (DRY Principle)**: นำการโหลด Database ซ้ำซ้อนใน Constructor ออกทั้งหมด และจัดกลุ่มฟังก์ชัน (Data Retrieval, Dropdown Helpers, Data Mutations) พร้อมเอกสาร PHPDoc ครบถ้วน
- **Type Safety & Lenient Input**: ปรับปรุง Method ให้รองรับ Type ทั้ง Int และ String อย่างปลอดภัย ป้องกัน TypeError เมื่อรับพารามิเตอร์จาก Request

### 2. ส่วน Controllers (`application/controllers/send_data.php`)
- **Centralized Helpers**: เพิ่ม Private Helpers สำหรับการจัดการ Response JSON (`_json_response`), การตรวจสอบสิทธิ์ (`_require_admin_auth`, `_require_employee_auth`) และการอัปโหลดไฟล์รูปภาพ (`_upload_employee_picture`)
- **Organized Structure**: จัดระเบียบโค้ดออกเป็น 10 หมวดหมู่อย่างชัดเจน:
  1. Section 1: Public / Frontend Pages & AJAX
  2. Section 2: Authentication & Session (Admin DB & LDAP)
  3. Section 3: Employee Dashboard
  4. Section 4: Admin Dashboard & Employee CRUD
  5. Section 5: Admin Function Management
  6. Section 6: Admin Department Management
  7. Section 7: Admin Section Management
  8. Section 8: Admin Map Management
  9. Section 9: Admin Position Management
  10. Section 10: Admin Account Management

### 3. ส่วน Views (`application/views/`)
- **Shared Partials (`views/partials/`)**: สร้าง `header.php` และ `footer.php` เป็นแกนกลางสำหรับโหลด CSS/JS, แสดงวันที่อัปเดตข้อมูลแบบไดนามิก และ Modal ยืนยัน Logout
- **Admin Modals Modularization (`views/admin/modals/`)**: แยก Modal ทั้ง 7 ตัวออกเป็นไฟล์แยก ลดความยาวและความซับซ้อนของ `admin_crud.php` และลบ Modal ซ้ำซ้อนเดิม
- **Native JavaScript Architecture**: ปรับให้ทุกหน้า View รัน JavaScript แบบ Native ในแท็ก `<script>` โดยตรง แก้ปัญหา Regex Escaping ใน PHP string (`replace(/'/g, ...)`) ทำให้ระบบโหลดข้อมูลได้รวดเร็วและไม่มี JavaScript Error

---

## 💻 เทคโนโลยีที่ใช้ (Tech Stack)

### Backend
- **Framework**: PHP CodeIgniter 3
- **Database**: Microsoft SQL Server / Active Record
- **Authentication**: Native DB Auth (Admin) & Active Directory LDAP (Employee)

### Frontend
- **Template / CSS**: AdminLTE 3, Bootstrap 4, FontAwesome 5
- **Plugins / Libraries**:
  - `jQuery 3.x`
  - `SweetAlert2` (UI แจ้งเตือนและยืนยันการทำรายการ)
  - `Select2` (Dropdown ค้นหาขั้นสูง)
  - `ExcelJS` & `FileSaver.js` (Export ข้อมูลเป็น Excel จากฝั่ง Client)

---

## ⚙️ การติดตั้งและตั้งค่า (Installation & Setup)

1. **Web Server Requirement**:
   - PHP 7.4+ หรือ PHP 8.x
   - ติดตั้ง Extensions: `php_sqlsrv`, `php_pdo_sqlsrv`, `php_ldap`
2. **การตั้งค่าฐานข้อมูล**:
   - กำหนดค่าการเชื่อมต่อฐานข้อมูลใน `application/config/database.php`
3. **การเข้าใช้งาน**:
   - **หน้าผู้ใช้ทั่วไป**: `http://localhost/ContactList/`
   - **เข้าสู่ระบบ Admin/Employee**: คลิกปุ่ม "Login" ที่มุมขวาบน
