#!/usr/bin/env bash
cloud=../../src
logic=../../src
protoc.exe --php_out=${cloud} --grpc_out=${cloud} --plugin=protoc-gen-grpc=./grpc_php_plugin.exe  cloud.proto
protoc.exe --php_out=${logic} --grpc_out=${logic} --plugin=protoc-gen-grpc=./grpc_php_plugin.exe  logic.proto
