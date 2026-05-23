Với case của bạn, AI nên được dùng để **nhân bản năng lực B2B sales**, không phải thay người sale. Mục tiêu đúng là:

- 1 SDR hiện tại làm được khối lượng prospecting của `3-5 người`
- 1 AE nhận lead đã được AI `nghiên cứu + chấm điểm + viết góc tiếp cận`
- founder chỉ vào các deal lớn hoặc account chiến lược

Cách triển khai hiệu quả nhất là dựng một `AI outbound factory` theo 7 workflow dưới đây.

**0. Hệ thống seeding đang dựng tại folder ugcmetrics **

**1. Dựng “bộ não bán hàng” trước**
Đây là bước nhiều đội bỏ qua, nên AI viết ra thứ nghe hay nhưng không chốt được.

Bạn cần gom vào một knowledge base:
- 50 khách salon tốt nhất hiện tại
- 50 deal thắng và 30 deal thua
- catalogue sản phẩm bán tốt
- các objection thật: giá, lead time, tóc có trộn không, lace, density, remake
- policy: MOQ, sample, đổi trả, exclusive territory, private label
- email/chat mẫu founder từng chốt được

Sau đó đưa toàn bộ vào một kho tri thức để AI tra cứu. OpenAI hỗ trợ `file search` và `vector stores` để model tìm đúng ngữ cảnh trong dữ liệu riêng của bạn trước khi viết nội dung; nếu cần xử lý hàng loạt thì dùng `Batch API`; nếu muốn đầu ra ổn định theo cột CRM thì dùng `Structured Outputs`.  
Nguồn: [OpenAI File Search](https://platform.openai.com/docs/guides/tools-file-search/), [OpenAI Batch API](https://platform.openai.com/docs/guides/batch/getting-started), [OpenAI Structured Outputs](https://platform.openai.com/docs/guides/structured-outputs?lang=javascript)

Ứng dụng ngay:
- AI không được phép tự bịa claim sản phẩm
- mọi email/copy đều phải bám đúng knowledge base của K-hair/5shair
- đầu ra bắt buộc theo schema:
  - `fit_score`
  - `salon_type`
  - `price_tier`
  - `likely_needs`
  - `best_offer`
  - `first_line`
  - `risk_flags`

**2. Workflow “Customer Finder” cho lead mới**
Mục tiêu là biến tìm khách hàng từ công việc thủ công sang dây chuyền bán tự động.

Nguồn lead nên đi theo 3 lớp:
- `Google Maps / Google Places`
- `Instagram / TikTok`
- `website directories / exhibitor lists / local salon associations`

Lưu ý thẳng: Google Maps Terms cấm scraping tự động nội dung Maps ra ngoài dịch vụ. Nếu làm bài bản, nên dùng `Google Places API` hoặc vendor compliant thay vì scrape thô.  
Nguồn: [Google Maps Platform Terms](https://cloud.google.com/maps-platform/terms?hl=en)

Cách làm thực tế:
- Dùng người hoặc VA lấy danh sách seed theo thành phố/ngách: `lace wig salon atlanta`, `hair extension salon houston`, `black hair salon london`
- AI enrich từng account:
  - salon có bán wig/extensions không
  - clientele thiên premium hay value
  - có private suite hay multi-stylist
  - có retail shelves hay chỉ service
  - chủ salon là ai
  - Instagram có hoạt động không
  - content gần đây có cho thấy đang thiếu nguồn cung / quảng bá wigs không

AI output:
- `A-tier`: nên tiếp cận ngay
- `B-tier`: cần nurture
- `C-tier`: loại

Nếu muốn đi nhanh, stack hợp lý là:
- `Clay` hoặc 'Hunter' để enrich, web research, scoring
- `OpenAI` để phân loại và viết hook

**3. Workflow “Research Agent” cho từng salon**
Đây là chỗ AI tạo khác biệt lớn nhất.

Mỗi salon trước khi outreach, AI phải tự tạo brief 1 trang:
- salon này bán gì
- texture/style họ đang đẩy
- nhóm khách chính
- mức giá ước tính
- buyer likely pain:
  - cần raw hair ổn định
  - cần faster restock
  - cần private label
  - cần better lace / density consistency
- angle nên mở đầu

Ví dụ schema research:
```json
{
  "account_name": "",
  "country": "",
  "city": "",
  "owner_name": "",
  "instagram_url": "",
  "services_offered": [],
  "retail_signals": [],
  "likely_buyer_type": "owner|manager|stylist",
  "price_tier": "premium|mid|value",
  "fit_score": 0,
  "pain_points": [],
  "suggested_offer": "",
  "opening_hook": "",
  "risk_flags": []
}
```

Đừng dùng AI để “viết email trước”, hãy dùng AI để **hiểu account trước**.

**4. Workflow “Message Factory” cho email / IG / WhatsApp**
Sai lầm phổ biến là cho AI viết email hàng loạt, kết quả nghe rất AI và chết response rate.

Cách đúng:
- AI chỉ viết `khung nháp 80%`
- sales sửa `20% cuối`
- mỗi message phải dựa trên research brief

Tôi khuyên chia 3 tầng personalization:
- `Tier 1`: top 5% account, cá nhân hóa sâu, có voice note/video
- `Tier 2`: account tốt, AI cá nhân hóa theo salon context
- `Tier 3`: broad list, chỉ cá nhân hóa theo segment

Cấu trúc email cold cold đầu tiên nên ngắn:
- 1 câu chứng minh bạn đã nghiên cứu salon
- 1 câu nêu lý do liên quan tới nguồn hàng/wig quality
- 1 câu offer rõ
- 1 CTA nhỏ

Ví dụ prompt nội bộ:
```text
Dựa trên account brief và knowledge base của K-hair, hãy viết:
- 2 subject lines
- 1 email đầu tiên dài dưới 90 từ
- 1 Instagram DM dưới 45 từ
- 1 WhatsApp message dưới 60 từ
Quy tắc:
- Không dùng hype words
- Không nói “best quality”
- Phải nêu 1 chi tiết cụ thể của salon
- CTA là sample/trial order, không ép call ngay nếu account nhỏ
```

Nếu bạn dùng HubSpot, có thể để AI + sequence chạy trong cùng CRM. HubSpot hiện có `Breeze`, `Prospecting Agent` và `Sequences`.  
Nguồn: [HubSpot Breeze overview](https://knowledge.hubspot.com/ai/understand-breeze?no_head=1), [HubSpot Prospecting Agent](https://knowledge.hubspot.com/prospecting/use-the-prospecting-agent?web=1), [HubSpot Sequences](https://knowledge.hubspot.com/sequences/create-and-edit-sequences?hsCtaAttrib=134468176552)

Nếu đội bạn không ở HubSpot, `Apollo` cũng đang đẩy mạnh AI prospecting và saved searches/context-based prospecting.  
Nguồn: [Apollo AI Assistant](https://knowledge.apollo.io/hc/en-us/articles/43512338043789-Find-Prospects-with-the-AI-Assistant), [Apollo Prospecting](https://knowledge.apollo.io/hc/en-us/articles/27155177775885-How-to-Prospect-in-Apollo)

**5. Workflow “Lead Scoring + Routing”**
AI phải giúp đội sales biết **đánh account nào trước**.

Tôi sẽ chấm lead theo 4 nhóm:
- `ICP fit`: salon có thực sự bán wigs/extensions không
- `commercial fit`: quy mô, nhiều stylists, có retail, có nhiều location
- `buying signal`: content gần đây, job openings, salon expansion, active social
- `ease of entry`: có email/IG/WhatsApp, owner visible, website có form

Công thức đơn giản:
- `A`: fit cao + signal rõ + contactable
- `B`: fit cao nhưng thiếu signal
- `C`: fit thấp hoặc khó tiếp cận

Quy tắc vận hành:
- SDR chỉ được phép chạm `A` và `B`
- `A` trong 24h phải được outreach
- `A` chưa phản hồi sau 7 ngày phải đổi channel
- account không có owner/contact rõ thì trả về enrich lại

**6. Workflow “WhatsApp Copilot”, nhưng làm đúng luật**
WhatsApp rất mạnh cho salon owner, nhưng không nên làm bừa. Với WhatsApp Business Platform, business-initiated outreach ngoài phiên user-initiated cần dùng template; một số luồng yêu cầu opt-in rõ.  
Nguồn: [WhatsApp Cloud API Quickstart](https://whatsapp.github.io/WhatsApp-Nodejs-SDK/), [WhatsApp template messages](https://whatsapp.github.io/WhatsApp-Nodejs-SDK/api-reference/messages/template/), [Microsoft Learn on WhatsApp templates and opt-in](https://learn.microsoft.com/en-gb/azure/communication-services/concepts/advanced-messaging/whatsapp/template-messages)

Cách dùng AI đúng:
- AI gợi ý nội dung follow-up theo lịch sử account
- AI tóm tắt toàn bộ email/IG trước đó để sales nhắn WhatsApp đúng ngữ cảnh
- AI đề xuất “next best action”:
  - gửi sample
  - hỏi texture đang bán mạnh
  - xin địa chỉ nhận catalog
  - chốt trial order

Không để AI tự bắn cold WhatsApp đại trà từ đầu. Hãy dùng WhatsApp chủ yếu cho:
- lead đã phản hồi ở kênh khác
- khách đã xin catalog/sample
- account cũ đang reorder
- trade show follow-up

**7. Workflow “Trade Show Miner”**
Đây là ROI rất cao vì exhibitor/attendee list thường sạch hơn lead lạnh.

Cách làm:
- tải exhibitor lists, attendee lists, sponsor lists của các show beauty/hair
- AI phân loại ai là salon, ai là distributor, ai là brand, ai là không phù hợp
- AI chấm priority
- AI viết intro riêng theo từng booth/company
- sau show, AI tạo follow-up sequence theo mức độ tương tác

Dùng `Batch API` để xử lý hàng nghìn company/profiles rất hợp, vì đây là job không cần realtime.  
Nguồn: [OpenAI Batch API](https://platform.openai.com/docs/guides/batch/getting-started)

**8. Workflow “Referral Engine” từ khách cũ**
Đây là kênh đội sales hay bỏ phí nhất.

AI có thể:
- xác định top 20 account hài lòng nhất
- tạo referral ask cá nhân hóa
- soạn message kiểu “nếu anh/chị biết salon nào cần raw Vietnamese wigs ổn định…”
- gợi ý incentive phù hợp theo lịch sử margin/account

Làm đơn giản:
- sau đơn thứ 3 hoặc tháng thứ 3, AI tạo task referral
- account manager chỉ việc duyệt và gửi

**9. Workflow “Reactivation Engine” cho khách ngủ**
Upload toàn bộ khách 6-12 tháng không đặt hàng.
AI sẽ phân nhóm:
- từng mua nhiều nhưng biến mất
- từng thử mẫu nhưng không mua
- từng than về giá
- từng than về quality
- từng mất do lead time

Mỗi nhóm cần một win-back offer khác nhau:
- `sample line mới`
- `better lead time`
- `small MOQ restock`
- `new raw line`
- `private label package`

Đây là chỗ AI rất mạnh vì nó đọc lịch sử chat và gợi ý đúng lý do quay lại.

**10. Stack tôi khuyên dùng ngay**
Nếu muốn triển khai nhanh, không build quá nặng:

Phương án thực dụng nhất:
- `Zenify` làm CRM + sequence + pipeline
- `Hunter` làm enrichment + research + scoring
- `OpenAI API` làm classification + personalization + win/loss learning
- `WhatsApp` cho follow-up
- `n8n` hoặc `Zapier` để nối workflow

Nếu cần ít kỹ thuật hơn:
- `Zenify + Hunter + OpenAI` là đủ để bắt đầu

Nếu cần linh hoạt và tự động hóa mạnh hơn:
- `Zenify + Hunter + OpenAI + n8n`

Lưu ý với `n8n`: nếu self-hosted thì phải quản trị quyền và vá bảo mật cẩn thận.  

**11. KPI để đo AI có thật sự giúp hay không**
Đừng đo số prompt hay số email viết ra. Đo:
- số account đủ chuẩn được tạo mỗi tuần
- thời gian research/account
- reply rate theo segment
- sample request rate
- trial order rate
- repeat order rate
- doanh thu trên mỗi rep
- số giờ founder còn phải trực tiếp prospect mỗi tuần

If sau 30 ngày mà:
- research time/account không giảm ít nhất `50%`
- SDR output không tăng ít nhất `2x`
- reply rate không cải thiện ở `A-tier`
thì hệ thống AI đó đang đẹp về mặt demo nhưng chưa giúp bán hàng.

**12. Lộ trình 30 ngày để áp dụng ngay**
Tuần 1:
- gom knowledge base
- chuẩn hóa ICP và schema scoring
- chọn stack

Tuần 2:
- dựng bảng lead master
- chạy workflow research + scoring cho 500 salon đầu tiên
- tạo 3 sequence cho `A/B/C tier`

Tuần 3:
- chạy outbound có human review
- bật reactivation cho khách cũ
- tạo dashboard KPI

Tuần 4:
- xem segment nào reply tốt nhất
- sửa prompt, scoring, offer
- tăng volume cho segment thắng

Điểm cuối cùng: **AI nên làm phần nghiên cứu, phân loại, soạn nháp, nhắc việc, và học từ dữ liệu cũ**. Phần chốt account lớn, thương lượng giá, xử lý trust và quality objection vẫn cần người thật.

