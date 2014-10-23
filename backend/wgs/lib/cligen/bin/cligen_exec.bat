@echo off
rem 【Cligenを利用するための準備について】
rem
rem 本バッチファイルは、以下の構成でCligenのモジュールがインストールされていることを前提に動作します。
rem モジュール取得先URL："http://www.ars.usda.gov/Research/docs.htm?docid=18094"
rem 「International Conversion Programs, Examples」(international.zip)
rem 　　インストール先：C:\data\wgs\lib\cligen\international
rem 「Cligen Version 5.3 Source Code」(cligenv53.zip)
rem 　　インストール先：C:\data\wgs\lib\cligen\cligenv53
rem International Conversion Programsは、実行時に必要な以下のファイルをインストールディレクトリに
rem 作成する必要があります。
rem 　　WEPP_CountryCodes.txt：インストールディレクトリのcountries.txtをコピーして作成する。
rem 　　AllStations.par：インストールディレクトリのAllStations.zipを解凍する。
rem
setlocal

rem Cligenの環境設定
set COMMAND1=C:\data\wgs\lib\cligen\international\GenStPar.exe
set COMMAND2=C:\data\wgs\lib\cligen\international\FindMatch.exe
set COMMAND3=C:\data\wgs\lib\cligen\cligenv53\cligen53.exe

set COUNTRY_CODES_FILE=C:\data\wgs\lib\cligen\international\WEPP_CountryCodes.txt
set ALL_STATIONS_FILE=C:\data\wgs\lib\cligen\international\AllStations.par

rem カレントディレクトリにGenStParコマンド実行に必要なファイルをコピー
echo ----- カレントディレクトリにWEPP_CountryCodes.txtをコピー
if not exist "WEPP_CountryCodes.txt" copy %COUNTRY_CODES_FILE% .
rem カレントディレクトリにFindMatchコマンド実行に必要なファイルをコピー
echo ----- カレントディレクトリにAllStations.parをコピー
if not exist "AllStations.par" copy %ALL_STATIONS_FILE% .

rem 引数からステーションIDを取得
set STATION_ID=%1
set START_YEAR=%2
set NUMBER_OF_YEARS=%3

echo ステーションID=%STATION_ID%
echo 開始年=%START_YEAR%
echo 年数=%NUMBER_OF_YEARS%

rem 生成ファイルが残っていれば削除する
if exist "%STATION_ID%.top" del %STATION_ID%.top
if exist "%STATION_ID%.par" del %STATION_ID%.par
if exist "%STATION_ID%.out" del %STATION_ID%.out

rem GenStParコマンド実行
echo ----- GenStParコマンド開始
echo %STATION_ID%.GDS|%COMMAND1%
echo ----- GenStParコマンド終了

rem FindMatchコマンド実行
echo %STATION_ID%.top>> COMMAND2_PARAM.txt
echo.>> COMMAND2_PARAM.txt
echo ----- FindMatchコマンド開始
%COMMAND2% < COMMAND2_PARAM.txt
echo ----- FindMatchコマンド終了
del COMMAND2_PARAM.txt

rem cligen53コマンド実行(5 - Multiple Year - WEPP Output File)
rem 入力ファイル、出力ファイル、開始年、シミュレート年数を指定する。
echo ----- cligen53コマンド開始
%COMMAND3% -i%STATION_ID%.par -o%STATION_ID%.out -b%START_YEAR% -y%NUMBER_OF_YEARS% -t5 >> cligen53.log
echo ----- cligen53コマンド終了

endlocal
