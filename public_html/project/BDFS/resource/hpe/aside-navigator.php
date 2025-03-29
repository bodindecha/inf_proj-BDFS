<center hidden><p>
    <a role="button" class="gray hollow dont-ripple" style="font-size: 12.5px;"
        data-href="/go?url=mailto%3Anoc%40bodin.ac.th%3Fsubject%3Dเว็บ%20INF-BD"
        href="/go?url=https%3A%2F%2Fmail.google.com%2Fa%2Fbodin.ac.th%2F%3Ffs%3D1%26tf%3Dcm%26to%3Dnoc%40bodin.ac.th%26su%3Dเว็บ%20INF-BD"
    target="_blank">ติดต่อสอบถาม/แจ้งปัญหาการใช้งาน</a>
</p></center>
<section class="nav">
    <style type="text/css">
        /* ALL version */
        aside.navigator_tab section.nav > ul { margin: 0px 0px 10px; padding-left: 5px !important; }
        aside.navigator_tab section.nav ul {
            padding-left: 25px;
            list-style-type: disc; white-space: nowrap;
        }
        aside.navigator_tab section.nav ul > li.this-page, aside.navigator_tab section.nav ul > li.this-page a {
            color: var(--clr-bs-indigo); font-weight: bold;
            pointer-events: none;
        }
        /* V1 */
        aside.navigator_tab section.nav ul > li {
            padding-right: 10px;
            width: fit-content; height: 20px; line-height: 20px;
        }
        aside.navigator_tab section.nav ul > li.sub-detail { list-style-type: none; }
        aside.navigator_tab section.nav ul > li.seperator {
            width: 80%; height: 10px;
            background-image: linear-gradient(to bottom, transparent 0%, transparent 42.5%, var(--fade-black-7) 42.5%, var(--fade-black-7) 57.5%, transparent 57.5%, transparent 100%);
            list-style-type: none;
        }
        /* V2 */
        aside.navigator_tab section.nav ul > li::marker { transform: translateX(5px); }
        aside.navigator_tab section.nav ul > li.epdb {
            min-height: 20px; height: auto;
            list-style-type: none;
        }
        aside.navigator_tab section.nav ul > li.epdb > details summary { transform: translateX(-10px); }
    </style>
    <ul>
        <?php if (!isset($_SESSION['auth']['type'])) { ?>
            <div class="group">
                <label>Sitemap</label>
                <ul>
                    <li><a href="/">หน้าหลัก (เข้าสู่ระบบ)</a></li>
                </ul>
            </div>
        <?php } else { if ($_SESSION['auth']['type'] == "s") { ?>
            <div class="group">
                <label><strong>Sitemap</strong></label>
                <ul>
                    <li><a href="/s/">หน้าหลัก (เมนู)</a></li>
                </ul>
            </div>
        <?php } else if ($_SESSION['auth']['type'] == "t") { $is_mod = $_SESSION['auth']['level']>=75; ?>
            <div class="group">
                <label>Sitemap</label>
                <ul>
                    <li><a href="/t/">หน้าหลัก (เมนู)</a></li>
                </ul>
            </div>
        <?php } ?>
            <details class="group" open>
                <summary>ธนาคารขยะรีไซเคิล</summary>
                <ul>
					<li><a href="/project/BDFS/trash-bank/my">สมุดบัญชีของฉัน</a></li>
					<li class="seperator">&nbsp;</li>
					<li><a href="/project/BDFS/trash-bank/moderate/">สำหรับเจ้าหน้าที่</a></li>
                </ul>
            </details>
            <details class="group" disabled>
                <summary>ของพี่ให้น้อง</summary>
                <ul>
					<li><a href="/project/BDFS/P-2-N/">...</a></li>
                </ul>
            </details>
            <details class="group" open>
                <summary>ของหายได้คืน</summary>
                <ul>
					<li><a href="/project/BDFS/LnF/post"><span>สร้างประกาศ</span></a></li>
					<li class="seperator">&nbsp;</li>
					<li><a href="/project/BDFS/LnF/list/lost"><span>รายการของหาย</span></a></li>
					<li><a href="/project/BDFS/LnF/list/found"><span>รายการของที่พบ</span></a></li>
                </ul>
            </details>
            <details class="group" disabled>
                <summary>หมู่บ้านปันสุข</summary>
                <ul>
					<li><a disabled href="/project/BDFS/punsuke-village/">...</a></li>
                </ul>
            </details>
            <li><a href="/" draggable="true"><span>โรงเรียนปลอดขยะ</span></a></li>
        <?php } ?>
    </ul>
</section>