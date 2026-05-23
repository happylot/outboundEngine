# Implementation Plan - Outbound Lead Gen Platform Upgrade

Kế hoạch này nâng cấp hệ thống hiện tại thành một **Outbound Lead Gen Platform** toàn diện (AI Outbound Factory), tập trung vào phân tích salon, chấm điểm tự động (Scoring), sinh kịch bản tiếp cận cá nhân hóa (Message Factory), và quản lý tái kích hoạt khách hàng cũ (Reactivation Engine) dựa trên các nguyên tắc mô tả trong `platform.md`.

## User Review Required

> [!IMPORTANT]
> - Chúng ta sẽ thiết lập **Sales Brain (Bộ não bán hàng)** lưu trữ tri thức objection, sản phẩm, và deal thắng để AI tham chiếu chéo khi viết thư tiếp cận, tránh tình trạng AI bịa đặt thông số.
> - Form nhập lead outbound sẽ hỗ trợ schema phân tích đầy đủ: `fit_score`, `price_tier`, `pain_points`, `risk_flags`, `suggested_offer`, v.v.
> - **Message Factory** sẽ sinh song song 3 định dạng: Cold Email (<90 từ), Instagram DM (<45 từ), và WhatsApp Message (<60 từ).

---

## Open Questions

> [!WARNING]
> 1. **Tích hợp Zenify CRM**: Có cần đẩy lead Outbound đã được làm ấm (warm) sang Zenify.cx tự động như lead Seeding không? (Khuyên dùng để đồng bộ phễu bán hàng).
> 2. **Nguồn dữ liệu nhập**: Giai đoạn này chúng ta sẽ nhập lead Outbound từ form thủ công hoặc danh sách giả lập, sau đó bấm nút "AI Research Agent" để tự động điền trường dữ liệu (enrich) hay bạn có muốn kết nối với API quét thực tế?

---

## Proposed Changes

### [Database & Models]

#### [NEW] [create_outbound_kb_entries_table.php](file:///Users/admin/Desktop/ugc/ugcmetrics/database/migrations/2026_05_23_130000_create_outbound_kb_entries_table.php)
*   Bảng `outbound_kb_entries` lưu tri thức:
    *   `id` (PK)
    *   `category` (objection, policy, catalog, winning_deal)
    *   `title` (String)
    *   `content` (Text)
    *   `timestamps`

#### [NEW] [create_outbound_leads_table.php](file:///Users/admin/Desktop/ugc/ugcmetrics/database/migrations/2026_05_23_131000_create_outbound_leads_table.php)
*   Bảng `outbound_leads` lưu lead Outbound:
    *   `id` (PK)
    *   `campaign_name` (String)
    *   `account_name` (String) - Tên salon/đối tác
    *   `owner_name` (String, Nullable)
    *   `instagram_url` (String, Nullable)
    *   `phone` (String, Nullable)
    *   `email` (String, Nullable)
    *   `website` (String, Nullable)
    *   `city` (String, Nullable)
    *   `country` (String, Nullable)
    *   `price_tier` (String) - premium, mid, value
    *   `fit_score` (Integer) - Chấm điểm 1-100
    *   `rating_tier` (String) - A, B, C
    *   `status` (String) - new, researching, researched, outreach_pending, outreached, replied, warm, lost, reactivating, referral_ask
    *   `services_offered` (JSON, Nullable)
    *   `retail_signals` (JSON, Nullable)
    *   `pain_points` (JSON, Nullable)
    *   `risk_flags` (JSON, Nullable)
    *   `suggested_offer` (Text, Nullable)
    *   `opening_hook` (Text, Nullable)
    *   `draft_email` (Text, Nullable)
    *   `draft_ig` (Text, Nullable)
    *   `draft_whatsapp` (Text, Nullable)
    *   `assigned_to` (String, Nullable) - SDR được gán
    *   `last_contacted_at` (DateTime, Nullable)
    *   `inactivity_reason` (String, Nullable) - Lý do ngủ đông (phục vụ Reactivation)
    *   `timestamps`

#### [NEW] [OutboundKbEntry.php](file:///Users/admin/Desktop/ugc/ugcmetrics/app/Models/OutboundKbEntry.php)
*   Eloquent Model cho `outbound_kb_entries`.

#### [NEW] [OutboundLead.php](file:///Users/admin/Desktop/ugc/ugcmetrics/app/Models/OutboundLead.php)
*   Eloquent Model cho `outbound_leads`.

---

### [Services]

#### [MODIFY] [AIService.php](file:///Users/admin/Desktop/ugc/ugcmetrics/app/Services/AIService.php)
*   Thêm phương thức `researchSalon(OutboundLead $lead)` gọi Gemini/Mock để tự động điền:
    *   `fit_score`
    *   `price_tier`
    *   `pain_points` (json)
    *   `risk_flags` (json)
    *   `suggested_offer`
    *   `opening_hook`
*   Thêm phương thức `generateOutboundDrafts(OutboundLead $lead)` sử dụng dữ liệu từ lead research kết hợp với các tri thức có liên quan từ `OutboundKbEntry` để viết nháp:
    *   Cold Email
    *   Instagram DM
    *   WhatsApp message

---

### [User Interface Components]

#### [NEW] [SalesBrain.php](file:///Users/admin/Desktop/ugc/ugcmetrics/app/Livewire/Outbound/SalesBrain.php) & [sales-brain.blade.php](file:///Users/admin/Desktop/ugc/ugcmetrics/resources/views/livewire/outbound/sales-brain.blade.php)
*   Giao diện quản lý "Bộ não bán hàng" (CRUD objections, policy, catalogues, deals mẫu).

#### [NEW] [OutboundLeads.php](file:///Users/admin/Desktop/ugc/ugcmetrics/app/Livewire/Outbound/OutboundLeads.php) & [outbound-leads.blade.php](file:///Users/admin/Desktop/ugc/ugcmetrics/resources/views/livewire/outbound/outbound-leads.blade.php)
*   Bảng điều khiển lead và Campaign Outbound:
    *   Danh sách salon, phân loại A/B/C tier, lọc tìm kiếm.
    *   Nút bấm import/tạo lead nhanh.

#### [NEW] [ResearchWorkspace.php](file:///Users/admin/Desktop/ugc/ugcmetrics/app/Livewire/Outbound/ResearchWorkspace.php) & [research-workspace.blade.php](file:///Users/admin/Desktop/ugc/ugcmetrics/resources/views/livewire/outbound/research-workspace.blade.php)
*   Phân hệ chi tiết nghiên cứu (Research) & Message Factory:
    *   Xem Profile Brief của salon.
    *   Nút kích hoạt AI Research điền nhanh thông số.
    *   Bảng soạn thảo nháp (Message Factory) hiển thị 3 tabs (Email, IG, WhatsApp) có hỗ trợ copy nhanh hoặc ghi nhận tương tác.

#### [NEW] [ReactivationEngine.php](file:///Users/admin/Desktop/ugc/outbound/ReactivationEngine.php) & [reactivation-engine.blade.php](file:///Users/admin/Desktop/ugc/ugcmetrics/resources/views/livewire/outbound/reactivation-engine.blade.php)
*   Giao diện quản trị "Khách ngủ đông" (Reactivation):
    *   Gom nhóm khách đã lâu không mua theo nguyên nhân (Giá, chất lượng, thời gian giao hàng...).
    *   AI tự động gợi ý Win-back Offer cá nhân hóa.

---

### [Routes & Layout Updates]

#### [MODIFY] [web.php](file:///Users/admin/Desktop/ugc/ugcmetrics/routes/web.php)
*   Thêm nhóm định tuyến `/outbound/...` cho 4 Livewire components trên.

#### [MODIFY] [sidebar.blade.php](file:///Users/admin/Desktop/ugc/ugcmetrics/resources/views/components/layouts/app/sidebar.blade.php)
*   Tạo phân mục điều hướng mới tên **"OUTBOUND SALES"** bao gồm 4 liên kết:
    1. **Sales Brain** (Icon: book-open)
    2. **Outbound Leads** (Icon: users)
    3. **Research Workspace** (Icon: clipboard-document-list)
    4. **Reactivation Engine** (Icon: arrow-path)

---

## Verification Plan

### Automated Tests
*   Viết test trong `tests/Feature/OutboundSalesTest.php` xác thực:
    *   Đồng bộ dữ liệu của AI Research Agent cho salon.
    *   Message Factory sinh đúng 3 tabs email/IG/WhatsApp dựa trên Sales Brain.
    *   Reactivation phân nhóm lý do và đề xuất win-back offer thành công.

### Manual Verification
*   Thực hiện thêm 1 salon ảo, bấm "Run AI Research" và kiểm tra xem kết quả chấm điểm A/B/C tier cùng brief nghiên cứu có hiển thị đúng giao diện.
*   Mở "Message Factory" xem nội dung nháp được tạo có chứa các keyword sản phẩm/objection từ Sales Brain hay không.
