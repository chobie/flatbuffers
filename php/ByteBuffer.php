<?php
/*
 * Copyright 2015 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Google\FlatBuffers;

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

    public static function wrap($bytes)
    {
        $bb = new ByteBuffer(0);
        $bb->_buffer = $bytes;

        return $bb;
    }

    /**
     * @param $size
     */
    public function __construct($size)
    {
        $this->_buffer = str_repeat("\0", $size);
    }

    /**
     * @return int
     */
    public function capacity()
    {
        return strlen($this->_buffer);
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->_pos;
    }

    /**
     * @param $pos
     */
    public function setPosition($pos)
    {
        $this->_pos = $pos;
    }

    /**
     *
     */
    public function reset()
    {
        $this->_pos = 0;
    }

    /**
     * @return int
     */
    public function length()
    {
        return strlen($this->_buffer);
    }

    /**
     * @return string
     */
    public function data()
    {
        return substr($this->_buffer, $this->_pos);
    }

    /**
     * @return bool
     */
    public static function isLittleEndian()
    {
        if (ByteBuffer::$_is_little_endian === null) {
            ByteBuffer::$_is_little_endian = unpack('S', "\x01\x00")[1] === 1;
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
    public function writeLittleEndian($offset, $count, $data)
    {
        if (ByteBuffer::isLittleEndian()) {
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
    public function readLittleEndian($offset, $count)
    {
        $this->assertOffsetAndLength($offset, $count);
        $r = 0;

        if (ByteBuffer::isLittleEndian()) {
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
    public function assertOffsetAndLength($offset, $length)
    {
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
    public function putSbyte($offset, $value)
    {
        $length = strlen($value);
        $this->assertOffsetAndLength($offset, $length);
        return $this->_buffer[$offset] = $value;
    }

    /**
     * @param $offset
     * @param $value
     * @return mixed
     */
    public function putByte($offset, $value)
    {
        $length = strlen($value);
        $this->assertOffsetAndLength($offset, $length);
        return $this->_buffer[$offset] = $value;
    }

    /**
     * @param $offset
     * @param $value
     */
    public function put($offset, $value)
    {
        $length = strlen($value);
        $this->assertOffsetAndLength($offset, $length);
        for ($i = 0; $i < $length; $i++) {
            $this->_buffer[$offset + $i] = $value[$i];
        }
    }

    /**
     * @param $offset
     * @param $value
     */
    public function putShort($offset, $value)
    {
        $this->assertOffsetAndLength($offset, 2);
        $this->writeLittleEndian($offset, 2, $value);
    }

    /**
     * @param $offset
     * @param $value
     */
    public function putUshort($offset, $value)
    {
        $this->assertOffsetAndLength($offset, 2);
        $this->writeLittleEndian($offset, 2, $value);
    }

    /**
     * @param $offset
     * @param $value
     */
    public function putInt($offset, $value)
    {
        $this->assertOffsetAndLength($offset, 4);
        $this->writeLittleEndian($offset, 4, $value);
    }

    /**
     * @param $offset
     * @param $value
     */
    public function putUint($offset, $value)
    {
        $this->assertOffsetAndLength($offset, 4);
        $this->writeLittleEndian($offset, 4, $value);
    }

    /**
     * @param $offset
     * @param $value
     */
    public function putLong($offset, $value)
    {
        $this->assertOffsetAndLength($offset, 8);
        $this->writeLittleEndian($offset, 8, $value);
    }

    /**
     * @param $offset
     * @param $value
     */
    public function putUlong($offset, $value)
    {
        $this->assertOffsetAndLength($offset, 8);
        $this->writeLittleEndian($offset, 8, $value);
    }

    /**
     * @param $offset
     * @param $value
     */
    public function putFloat($offset, $value)
    {
        $this->assertOffsetAndLength($offset, 4);

        $floathelper = pack("f", $value);
        $v = unpack("V", $floathelper);
        $this->writeLittleEndian($offset, 4, $v[1]);
    }

    /**
     * @param $offset
     * @param $value
     */
    public function putDouble($offset, $value)
    {
        $this->assertOffsetAndLength($offset, 8);

        $floathelper = pack("d", $value);
        $v = unpack("V*", $floathelper);

        $this->writeLittleEndian($offset, 4, $v[1]);
        $this->writeLittleEndian($offset + 4, 4, $v[2]);
    }

    /**
     * @param $index
     * @return mixed
     */
    public function getByte($index)
    {
        return ord($this->_buffer[$index]);
    }

    /**
     * @param $index
     * @return mixed
     */
    public function getSbyte($index)
    {
        $v = unpack("c", $this->_buffer[$index]);
        return $v[1];
    }

    /**
     * @param $buffer
     */
    public function getX(&$buffer)
    {
        for ($i = $this->_pos, $j = 0; $j < strlen($buffer); $i++, $j++) {
            $buffer[$j] = $this->_buffer[$i];
        }
    }

    /**
     * @param $index
     * @return mixed
     */
    public function get($index)
    {
        $this->assertOffsetAndLength($index, 1);
        return $this->_buffer[$index];
    }


    /**
     * @param $index
     * @return mixed
     */
    public function getBool($index)
    {
        return (bool)ord($this->_buffer[$index]);
    }

    /**
     * @param $index
     * @return int
     */
    public function getShort($index)
    {
        $result = $this->readLittleEndian($index, 2);
        $helper = pack("v", $result);
        $v = unpack("s", $helper);

        return $v[1];
    }

    /**
     * @param $index
     * @return int
     */
    public function getUShort($index)
    {
        return $this->readLittleEndian($index, 2);
    }

    /**
     * @param $index
     * @return int
     */
    public function getInt($index)
    {
        $result = $this->readLittleEndian($index, 4);

        $helper = pack("V", $result);
        $v = unpack("l", $helper);
        return $v[1];
    }

    /**
     * @param $index
     * @return int
     */
    public function getUint($index)
    {
        return $this->readLittleEndian($index, 4);
    }

    /**
     * @param $index
     * @return int
     */
    public function getLong($index)
    {
        $result =  $this->readLittleEndian($index, 8);

        $helper = pack("P", $result);
        $v = unpack("q", $helper);
        return $v[1];
    }

    /**
     * @param $index
     * @return int
     */
    public function getUlong($index)
    {
        return $this->readLittleEndian($index, 8);
    }

    /**
     * @param $index
     * @return mixed
     */
    public function getFloat($index)
    {
        $i = $this->readLittleEndian($index, 4);
        $inthelper = pack("V", $i);
        $v = unpack("f", $inthelper);

        return $v[1];
    }

    /**
     * @param $index
     * @return float
     */
    public function getDouble($index)
    {
        $i = $this->readLittleEndian($index, 4);
        $i2 = $this->readLittleEndian($index + 4, 4);
        $inthelper = pack("VV", $i, $i2);
        $v = unpack("d", $inthelper);

        return $v[1];
    }
}
