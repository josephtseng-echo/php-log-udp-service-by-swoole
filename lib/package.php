<?php
/**
 *
 * @author JosephZeng
 *
 */
class ReadPackage {
	
	private $m_Offset = 0;
	private $package_realsize = 0;
	
	public $packet_buffer;
	public $cmdType = 0;
	public $version = 0;
	public $reserved = 0;

	const HEARD_SIZE = 11;

	public function ReadPackageBuffer($buff) {
		$this->packet_buffer ='';
		$this->m_Offset=0;
		$this->package_realsize = strlen($buff);
		if ($this->package_realsize < self::HEARD_SIZE) {
			return false;
		}
		$headerInfo = unpack("c2Iden/scmdType/cVer/Nreserved/sLen", substr($buff, 0, self::HEARD_SIZE));
		$this->cmdType = $headerInfo['cmdType'];
		$this->version = $headerInfo['Ver'];
		$this->reserved = $headerInfo['reserved'];
		$len = $headerInfo['Len'];
		if (($len + self::HEARD_SIZE) !== $this->package_realsize) {
			return false;
		}	
		$this->m_Offset = self::HEARD_SIZE;	
		$this->packet_buffer = $buff;
		return true;
	}

	public function ReadByte() {
		if ($this->package_realsize <= $this->m_Offset) {
			throw new VerifyException("读取溢出");
		}
		$temp = substr($this->packet_buffer, $this->m_Offset, 1);
		if ($temp === false) {
			throw new VerifyException("读取溢出");
		}
		$value = unpack("C", $temp);
		$this->m_Offset+=1;
		return $value[1];
	}

	public function ReadShort() {
		if ($this->package_realsize <= $this->m_Offset) {
			throw new VerifyException("读取溢出");
		}
		$temp = substr($this->packet_buffer, $this->m_Offset, 2);
		if ($temp === false) {
			throw new VerifyException("读取溢出");
		}
		$value = unpack("s", $temp);
		$this->m_Offset+=2;
		return $value[1];
	}

	public function ReadInt() {
		if ($this->package_realsize <= $this->m_Offset) {
			throw new VerifyException("读取溢出");
		}
		$temp = substr($this->packet_buffer, $this->m_Offset, 4);
		if ($temp === false) {
			throw new VerifyException("读取溢出");
		}
		$value = unpack("i", $temp);
		$this->m_Offset+=4;
		return $value[1];
	}

	public function ReadUInt() {
		if ($this->package_realsize <= $this->m_Offset) {
			throw new VerifyException("读取溢出");
		}
		$temp = substr($this->packet_buffer, $this->m_Offset, 4);
		if ($temp === false) {
			throw new VerifyException("读取溢出");
		}
		list(, $var_unsigned) = unpack("L", $temp);
		$this->m_Offset+=4;
		return floatval(sprintf("%u", $var_unsigned));
	}

	public function ReadString() {
		if ($this->package_realsize <= $this->m_Offset) {
			throw new VerifyException("读取溢出");
		}
		$len = $this->ReadUInt();
		if ($len === false) {
			throw new VerifyException("读取溢出");
		}
		$realLen = $this->package_realsize - $this->m_Offset;
		if ($realLen < $len) {
			throw new VerifyException("读取溢出");
		}
		$value = substr($this->packet_buffer, $this->m_Offset, $len - 1);
		$this->m_Offset+=$len;
		return $value;
	}
	
	public function GetLen(){
		return $this->package_realsize;
	}

}

class WritePackage {

	public $len = 0;
	private $packet_buffer;
	public $cmdType = 0;
	public $version = 0;
	public $reserved = 0;

	public function WriteBegin($cmdType, $version = 1, $reserved = 0) {
		$this->cmdType = $cmdType;
		$this->packet_buffer = "";
		$this->len = 0;
		$this->reserved = $reserved;
		$this->version = $version;
	}

	public function WriteString($value) {
		$len = strlen($value);
		$this->packet_buffer.=pack("L", $len + 1);
		if ($len > 0) {
			$this->packet_buffer.=$value;
		}
		$this->packet_buffer.=pack("C", 0);
		$this->len+=$len + 1 + 4;
	}

	public function WriteInt($value) {
		$this->packet_buffer.=pack("i", $value);
		$this->len+=4;
	}

	public function WriteUInt($value) {
		$this->packet_buffer.=pack("L", $value);
		$this->len+=4;
	}

	public function WriteByte($value) {
		$this->packet_buffer.=pack("C", $value);
		$this->len+=1;
	}

	public function WriteShort($value) {
		$this->packet_buffer.=pack("s", $value);
		$this->len+=2;
	}

	public function WriteEnd() {
		if ($this->len > 65500) {
			throw new VerifyException("数据包不能超出65500字节");
		}
		$this->packet_buffer = "XO" . pack('s', $this->cmdType) . pack('C', $this->version) . pack('N', $this->reserved) . pack('s', $this->len) . $this->packet_buffer;
		return $this->packet_buffer;
	}

	public function GetPacketBuffer() {
		return $this->packet_buffer;
	}

}