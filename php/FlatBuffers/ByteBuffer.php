<?php
namespace FlatBuffers;

class ByteBuffer
{
    /**
     * @var string $_buffer;
     */
    public $_buffer;

    /**
     * @var int $_pos;
     */
    private $_pos;

    /**
     * @var bool $_is_little_endian
     */
    private static $_is_little_endian = null;

    private $floathelper = "";
    private $inthelper = "";
    private $doublehelper = "";
    private $ulonghelper = "";

    public static function Wrap(&$bytes)
    {
        $bb = new ByteBuffer(0);
        $bb->_buffer = &$bytes;

        return $bb;
    }

    /**
     * @param $size
     */
    public function __construct($size)
    {
        $this->_buffer = str_repeat("\0", $size);

        $this->floathelper = pack("f", 0);
        $this->inthelper = pack("V", 0);
        $this->doublehelper = pack("d", 0);
        $this->ulonghelper = "\0\0\0\0\0\0\0\0";
    }

    /**
     * @return int
     */
    public function Capacity()
    {
        return strlen($this->_buffer);
    }

    /**
     * @return int
     */
    public function GetPosition()
    {
        return $this->_pos;
    }

    /**
     * @param $pos
     */
    public function SetPosition($pos)
    {
        $this->_pos = $pos;
    }

    /**
     *
     */
    public function Reset()
    {
        $this->_pos = 0;
    }

    /**
     * @return int
     */
    public function Length()
    {
        return strlen($this->_buffer);
    }

    /**
     * @return string
     */
    public function Data()
    {
        return substr($this->_buffer, $this->_pos);
    }

    /**
     * @return bool
     */
    public static function IsLittleEndian()
    {
        if (ByteBuffer::$_is_little_endian === null) {
            ByteBuffer::$_is_little_endian = unpack('S',"\x01\x00")[1] === 1;
        }

        return ByteBuffer::$_is_little_endian;
    }

    /**
     * write little endian value to the buffer.
     *
     * @param $offset
     * @param $count byte length
     * @param $data actual values
     */
    public function WriteLittleEndian($offset, $count, $data)
    {
        if (ByteBuffer::IsLittleEndian()) {
            for ($i = 0; $i < $count; $i++) {
                $this->_buffer[$offset + $i] = chr($data >> $i * 8);
            }
        } else {
            for ($i = 0; $i < $count; $i++) {
                $this->_buffer[$offset + $count - 1 - $i] = chr($data >> $i * 8);
            }
        }
    }

    /**
     * read little endian value from the buffer
     *
     * @param $offset
     * @param $count acutal size
     * @return int
     */
    public function ReadLittleEndian($offset, $count) {
        $this->AssertOffsetAndLength($offset, $count);
        $r = 0;

        if (ByteBuffer::IsLittleEndian()) {
            for ($i = 0; $i < $count; $i++) {
                $r |= ord($this->_buffer[$offset + $i]) << $i * 8;
            }
        } else {
            for ($i = 0; $i < $count; $i++) {
                $r |= ord($this->_buffer[$offset + $count -1 - $i]) << $i * 8;
            }
        }

        return $r;
    }

    /**
     * @param $offset
     * @param $length
     */
    public function AssertOffsetAndLength($offset, $length) {
        if ($offset < 0 ||
            $offset >= strlen($this->_buffer) ||
            $offset + $length > strlen($this->_buffer)) {

            throw new \OutOfRangeException(sprintf("offset: %d, length: %d, buffer; %d", $offset, $length, strlen($this->_buffer)));
        }
    }

    /**
     * @param $offset
     * @param $value
     * @return mixed
     */
    public function PutSbyte($offset, $value) {
        $length = strlen($value);
        $this->AssertOffsetAndLength($offset, $length);
        return $this->_buffer[$offset] = $value;
    }

    /**
     * @param $offset
     * @param $value
     * @return mixed
     */
    public function PutByte($offset, $value) {
        $length = strlen($value);
        $this->AssertOffsetAndLength($offset, $length);
        return $this->_buffer[$offset] = $value;
    }

    /**
     * @param $offset
     * @param $value
     */
    public function PutX($offset, $value) {
        $length = strlen($value);
        $this->AssertOffsetAndLength($offset, $length);
        for ($i = 0; $i < $length; $i++) {
            $this->_buffer[$offset + $i] = $value[$i];
        }
    }

    /**
     * @param $offset
     * @param $value
     */
    public function PutShortX($offset, $value) {
        $this->AssertOffsetAndLength($offset, 2);
        $this->WriteLittleEndian($offset, 2, $value);
    }

    /**
     * @param $offset
     * @param $value
     */
    public function PutUshortX($offset, $value) {
        $this->AssertOffsetAndLength($offset, 2);
        $this->WriteLittleEndian($offset, 2, $value);
    }

    /**
     * @param $offset
     * @param $value
     */
    public function PutUshort($offset, $value) {
        $this->AssertOffsetAndLength($offset, 2);
        $this->WriteLittleEndian($offset, 2, $value);
    }

    /**
     * @param $offset
     * @param $value
     */
    public function PutIntX($offset, $value) {
        $this->AssertOffsetAndLength($offset, 4);
        $this->WriteLittleEndian($offset, 4, $value);
    }

    /**
     * @param $offset
     * @param $value
     */
    public function PutUintX($offset, $value) {
        $this->AssertOffsetAndLength($offset, 4);
        $this->WriteLittleEndian($offset, 4, $value);
    }

    /**
     * @param $offset
     * @param $value
     */
    public function PutUint($offset, $value) {
        $this->AssertOffsetAndLength($offset, 4);
        $this->WriteLittleEndian($offset, 4, $value);
    }

    /**
     * @param $offset
     * @param $value
     */
    public function PutLongX($offset, $value) {
        $this->AssertOffsetAndLength($offset, 8);
        $this->WriteLittleEndian($offset, 8, $value);
    }

    /**
     * @param $offset
     * @param $value
     */
    public function PutUlongX($offset, $value) {
        $this->AssertOffsetAndLength($offset, 8);
        $this->WriteLittleEndian($offset, 8, $value);
    }

    /**
     * @param $offset
     * @param $value
     */
    public function PutFloatX($offset, $value) {
        $this->AssertOffsetAndLength($offset, 4);

        $floathelper = pack("f", $value);
        $v = unpack("V", $floathelper);
        $this->WriteLittleEndian($offset, 4, $v[1]);
    }

    /**
     * @param $offset
     * @param $value
     */
    public function PutDoubleX($offset, $value) {
        $this->AssertOffsetAndLength($offset, 8);

        $floathelper = pack("d", $value);
        $v = unpack("V*", $floathelper);

        $this->WriteLittleEndian($offset, 4, $v[1]);
        $this->WriteLittleEndian($offset + 4, 4, $v[2]);
    }

    /**
     * @param $index
     * @return mixed
     */
    public function GetByte($index) {
        return ord($this->_buffer[$index]);
    }

    /**
     * @param $index
     * @return mixed
     */
    public function GetSbyte($index) {
        $v = unpack("c", $this->_buffer[$index]);
        return $v[1];
    }

    /**
     * @param $buffer
     */
    public function GetX(&$buffer) {
        for ($i = $this->_pos, $j = 0; $j < strlen($buffer); $i++, $j++) {
            $buffer[$j] = $this->_buffer[$i];
        }
    }

    /**
     * @param $index
     * @return mixed
     */
    public function Get($index) {
        $this->AssertOffsetAndLength($index, 1);
        return $this->_buffer[$index];
    }


    /**
     * @param $index
     * @return mixed
     */
    public function GetBool($index) {
        return (bool)ord($this->_buffer[$index]);
    }

    /**
     * @param $index
     * @return int
     */
    public function GetShort($index) {
        $result = $this->ReadLittleEndian($index, 2);
        $helper = pack("v", $result);
        $v = unpack("s", $helper);

        return $v[1];
    }

    /**
     * @param $index
     * @return int
     */
    public function GetUShort($index) {
        return $this->ReadLittleEndian($index, 2);
    }

    /**
     * @param $index
     * @return int
     */
    public function GetInt($index) {
        $result = $this->ReadLittleEndian($index, 4);

        $helper = pack("V", $result);
        $v = unpack("l", $helper);
        return $v[1];
    }

    /**
     * @param $index
     * @return int
     */
    public function GetUint($index) {
        return $this->ReadLittleEndian($index, 4);
    }

    /**
     * @param $index
     * @return int
     */
    public function GetLong($index) {
        $result =  $this->ReadLittleEndian($index, 8);

        $helper = pack("P", $result);
        $v = unpack("q", $helper);
        return $v[1];
    }

    /**
     * @param $index
     * @return int
     */
    public function GetUlong($index) {
        return $this->ReadLittleEndian($index, 8);
    }

    /**
     * @param $index
     * @return mixed
     */
    public function GetFloat($index) {
        $i = $this->ReadLittleEndian($index, 4);
        $inthelper = pack("V", $i);
        $v = unpack("f", $inthelper);

        return $v[1];
    }

    /**
     * @param $index
     * @return float
     */
    public function GetDouble($index) {
        $i = $this->ReadLittleEndian($index, 4);
        $i2 = $this->ReadLittleEndian($index + 4, 4);
        $inthelper = pack("VV", $i, $i2);
        $v = unpack("d", $inthelper);

        return $v[1];
    }
}
