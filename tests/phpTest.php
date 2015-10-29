<?php
// manual load for testing. please use PSR style autoloader when you use flatbuffers.
require join(DIRECTORY_SEPARATOR, array(dirname(dirname(__FILE__)), "php", "FlatBuffers", "Constants.php"));
require join(DIRECTORY_SEPARATOR, array(dirname(dirname(__FILE__)), "php", "FlatBuffers", "ByteBuffer.php"));
require join(DIRECTORY_SEPARATOR, array(dirname(dirname(__FILE__)), "php", "FlatBuffers", "FlatbufferBuilder.php"));
require join(DIRECTORY_SEPARATOR, array(dirname(dirname(__FILE__)), "php", "FlatBuffers", "Table.php"));
require join(DIRECTORY_SEPARATOR, array(dirname(dirname(__FILE__)), "php", "FlatBuffers", "Struct.php"));
foreach (glob(join(DIRECTORY_SEPARATOR, array(dirname(__FILE__), "MyGame", "Example", "*.php"))) as $file) {
    require $file;
}

function main()
{
    /// Begin Test
    $assert = new Assert();

    // First, let's test reading a FlatBuffer generated by C++ code:
    // This file was generated from monsterdata_test.json

    // Now test it:
    $data = file_get_contents('monsterdata_test.mon');
    $bb = FlatBuffers\ByteBuffer::Wrap($data);
    test_buffer($assert, $bb);

    // Second, let's create a FlatBuffer from scratch in JavaScript, and test it also.
    // We use an initial size of 1 to exercise the reallocation algorithm,
    // normally a size larger than the typical FlatBuffer you generate would be
    // better for performance.
    $fbb = new FlatBuffers\FlatBufferBuilder(1);

    // We set up the same values as monsterdata.json:
    $str = $fbb->CreateString("MyMonster");

    $inv = \MyGame\Example\Monster::CreateInventoryVector($fbb, array(0, 1, 2, 3, 4));

    $fred = $fbb->CreateString('Fred');
    \MyGame\Example\Monster::StartMonster($fbb);
    \MyGame\Example\Monster::AddName($fbb, $fred);
    $mon2 = \MyGame\Example\Monster::EndMonster($fbb);

    \MyGame\Example\Monster::StartTest4Vector($fbb, 2);
    \MyGame\Example\Test::CreateTest($fbb, 10, 20);
    \MyGame\Example\Test::CreateTest($fbb, 30, 40);
    $test4 = $fbb->endVector();

    $testArrayOfString = \MyGame\Example\Monster::CreateTestarrayofstringVector($fbb, array(
        $fbb->CreateString('test1'),
        $fbb->CreateString('test2')
    ));

    \MyGame\Example\Monster::StartMonster($fbb);
    \MyGame\Example\Monster::AddPos($fbb, \MyGame\Example\Vec3::CreateVec3($fbb,
        1.0, 2.0, 3.0, //float
        3.0, // double
        \MyGame\Example\Color::Green,
        5, //short
        6));
    \MyGame\Example\Monster::AddHp($fbb, 80);
    \MyGame\Example\Monster::AddName($fbb, $str);
    \MyGame\Example\Monster::AddInventory($fbb, $inv);
    \MyGame\Example\Monster::AddTestType($fbb, \MyGame\Example\Any::Monster);
    \MyGame\Example\Monster::AddTest($fbb, $mon2);
    \MyGame\Example\Monster::AddTest4($fbb, $test4);
    \MyGame\Example\Monster::AddTestarrayofstring($fbb, $testArrayOfString);
    \MyGame\Example\Monster::AddTestbool($fbb, false);
    $mon = \MyGame\Example\Monster::EndMonster($fbb);

    \MyGame\Example\Monster::FinishMonsterBuffer($fbb, $mon);

    // Test it:
    test_buffer($assert, $fbb->dataBuffer());

//TODO
//    testUnicode();
//    fuzzTest1();

    echo 'FlatBuffers php test: completed successfully' . PHP_EOL;
}

try {
    main();
    exit(0);
} catch(Exception $e) {
    printf("Fatal error: Uncaught exception '%s' with message '%s. in %s:%d\n", get_class($e), $e->getMessage(), $e->getFile(), $e->getLine());
    printf("Stack trace:\n");
    echo $e->getTraceAsString() . PHP_EOL;
    printf("  thrown in in %s:%d\n", $e->getFile(), $e->getLine());

    die(-1);
}

function test_buffer(Assert $assert, \FlatBuffers\ByteBuffer $bb) {

    $assert->ok(MyGame\Example\Monster::MonsterBufferHasIdentifier($bb));
    $monster = \MyGame\Example\Monster::GetRootAsMonster($bb);

    $assert->strictEqual($monster->GetHp(), 80);
    $assert->strictEqual($monster->GetMana(), 150); // default

    $assert->strictEqual($monster->GetName(), 'MyMonster');

    $pos = $monster->GetPos();
    $assert->strictEqual($pos->GetX(), 1.0);
    $assert->strictEqual($pos->GetY(), 2.0);
    $assert->strictEqual($pos->GetZ(), 3.0);

    $assert->Equal($pos->GetTest1(), 3.0);
    $assert->strictEqual($pos->GetTest2(), \MyGame\Example\Color::Green);

    $t = $pos->GetTest3();
    $assert->strictEqual($t->GetA(), 5);
    $assert->strictEqual($t->GetB(), 6);
    $assert->strictEqual($monster->GetTestType(), \MyGame\Example\Any::Monster);

    $monster2 = new \MyGame\Example\Monster();
    $assert->strictEqual($monster->GetTest($monster2) != null, true);
    $assert->strictEqual($monster2->GetName(), 'Fred');

    $assert->strictEqual($monster->GetInventoryLength(), 5);
    $invsum = 0;
    for ($i = 0; $i < $monster->GetInventoryLength(); $i++) {
        $invsum += $monster->GetInventory($i);
    }
    $assert->strictEqual($invsum, 10);


    $test_0 = $monster->GetTest4(0);
    $test_1 = $monster->GetTest4(1);
    $assert->strictEqual($monster->GetTest4Length(), 2);
    $assert->strictEqual($test_0->GetA() + $test_0->GetB() + $test_1->GetA() + $test_1->GetB(), 100);

    $assert->strictEqual($monster->GetTestarrayofstringLength(), 2);
    $assert->strictEqual($monster->GetTestarrayofstring(0), 'test1');
    $assert->strictEqual($monster->GetTestarrayofstring(1), 'test2');
    $assert->strictEqual($monster->GetTestbool(), false);
}

// TODO
function testUnicode(Assert $assert) {
    $correct = file_get_contents('unicode_test.mon');
    $json = json_decode(file_get_contents('unicode_test.json'));

    // Test reading
    $bb = flatbuffers\ByteBuffer::Wrap($correct);
    $monster = \MyGame\Example\Monster::GetRootAsMonster($bb);
    $assert->strictEqual($monster->GetName(), $json["name"]);

    //$assert->deepEqual(new Buffer(monster.name(flatbuffers.Encoding.UTF8_BYTES)), new Buffer(json.name));
    //assert.strictEqual(monster.testarrayoftablesLength(), json.testarrayoftables.length);
    foreach ($json["testarrayoftables"]as $i => $table) {
        $value = $monster->GetTestArrayOfTables($i);
        $assert->strictEqual($value->GetName(), $table["name"]);
        //assert.deepEqual(new Buffer(value.name(flatbuffers.Encoding.UTF8_BYTES)), new Buffer(table.name));
    }
    $assert->strictEqual($monster->GetTestarrayofstringLength(), $json["testarrayofstring"]["length"]);
    foreach ($json["testarrayofstring"] as $i => $string) {
        $assert->strictEqual($monster->GetTestarrayofstring($i), $string);
        //assert.deepEqual(new Buffer(monster.testarrayofstring(i, flatbuffers.Encoding.UTF8_BYTES)), new Buffer(string));
    }

    // Test writing
    $fbb = new FlatBuffers\FlatBufferBuilder(1);
    $name = $fbb->CreateString($json["name"]);
    $testarrayoftablesOffsets = array_map(function($table) use($fbb) {
        $name = $fbb->CreateString($table["name"]);
        \MyGame\Example\Monster::StartMonster($fbb);
        \MyGame\Example\Monster::AddName($fbb, $name);
        return \MyGame\Example\Monster::EndMonster($fbb);
    }, $json["testarrayoftables"]);
    $testarrayoftablesOffset = \MyGame\Example\Monster::CreateTestarrayoftablesVector($fbb,
            $testarrayoftablesOffsets);
//    $testarrayofstringOffset = \MyGame\Example\Monster::CreateTestarrayofstringVector($fbb,
//            $json["testarrayofstring"].map(function(string) { return fbb.createString(string); }));

    \MyGame\Example\Monster::startMonster($fbb);
    \MyGame\Example\Monster::addTestarrayofstring($fbb, $testarrayoftablesOffset);
    \MyGame\Example\Monster::addTestarrayoftables($fbb, $testarrayoftablesOffset);
    \MyGame\Example\Monster::addName($fbb, $name);
    \MyGame\Example\Monster::finishMonsterBuffer($fbb, \MyGame\Example\Monster::endMonster($fbb));
    //;assert.deepEqual(new Buffer(fbb.asUint8Array()), correct);
}

class Assert {
    public function ok($result, $message = "") {
        if (!$result){
            throw new Exception(!empty($message) ? $message : "{$result} is not true.");
        }
    }

    public function Equal($result, $expected, $message = "") {
        if ($result != $expected) {
            throw new Exception(!empty($message) ? $message : "given the result {$result} is not equals as {$expected}");
        }
    }


    public function strictEqual($result, $expected, $message = "") {
        if ($result !== $expected) {
            throw new Exception(!empty($message) ? $message : "given the result {$result} is not strict equals as {$expected}");
        }
    }
}
