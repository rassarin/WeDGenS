@echo off
rem �yCligen�𗘗p���邽�߂̏����ɂ��āz
rem
rem �{�o�b�`�t�@�C���́A�ȉ��̍\����Cligen�̃��W���[�����C���X�g�[������Ă��邱�Ƃ�O��ɓ��삵�܂��B
rem ���W���[���擾��URL�F"http://www.ars.usda.gov/Research/docs.htm?docid=18094"
rem �uInternational Conversion Programs, Examples�v(international.zip)
rem �@�@�C���X�g�[����FC:\data\wgs\lib\cligen\international
rem �uCligen Version 5.3 Source Code�v(cligenv53.zip)
rem �@�@�C���X�g�[����FC:\data\wgs\lib\cligen\cligenv53
rem International Conversion Programs�́A���s���ɕK�v�Ȉȉ��̃t�@�C�����C���X�g�[���f�B���N�g����
rem �쐬����K�v������܂��B
rem �@�@WEPP_CountryCodes.txt�F�C���X�g�[���f�B���N�g����countries.txt���R�s�[���č쐬����B
rem �@�@AllStations.par�F�C���X�g�[���f�B���N�g����AllStations.zip���𓀂���B
rem
setlocal

rem Cligen�̊��ݒ�
set COMMAND1=C:\data\wgs\lib\cligen\international\GenStPar.exe
set COMMAND2=C:\data\wgs\lib\cligen\international\FindMatch.exe
set COMMAND3=C:\data\wgs\lib\cligen\cligenv53\cligen53.exe

set COUNTRY_CODES_FILE=C:\data\wgs\lib\cligen\international\WEPP_CountryCodes.txt
set ALL_STATIONS_FILE=C:\data\wgs\lib\cligen\international\AllStations.par

rem �J�����g�f�B���N�g����GenStPar�R�}���h���s�ɕK�v�ȃt�@�C�����R�s�[
echo ----- �J�����g�f�B���N�g����WEPP_CountryCodes.txt���R�s�[
if not exist "WEPP_CountryCodes.txt" copy %COUNTRY_CODES_FILE% .
rem �J�����g�f�B���N�g����FindMatch�R�}���h���s�ɕK�v�ȃt�@�C�����R�s�[
echo ----- �J�����g�f�B���N�g����AllStations.par���R�s�[
if not exist "AllStations.par" copy %ALL_STATIONS_FILE% .

rem ��������X�e�[�V����ID���擾
set STATION_ID=%1
set START_YEAR=%2
set NUMBER_OF_YEARS=%3

echo �X�e�[�V����ID=%STATION_ID%
echo �J�n�N=%START_YEAR%
echo �N��=%NUMBER_OF_YEARS%

rem �����t�@�C�����c���Ă���΍폜����
if exist "%STATION_ID%.top" del %STATION_ID%.top
if exist "%STATION_ID%.par" del %STATION_ID%.par
if exist "%STATION_ID%.out" del %STATION_ID%.out

rem GenStPar�R�}���h���s
echo ----- GenStPar�R�}���h�J�n
echo %STATION_ID%.GDS|%COMMAND1%
echo ----- GenStPar�R�}���h�I��

rem FindMatch�R�}���h���s
echo %STATION_ID%.top>> COMMAND2_PARAM.txt
echo.>> COMMAND2_PARAM.txt
echo ----- FindMatch�R�}���h�J�n
%COMMAND2% < COMMAND2_PARAM.txt
echo ----- FindMatch�R�}���h�I��
del COMMAND2_PARAM.txt

rem cligen53�R�}���h���s(5 - Multiple Year - WEPP Output File)
rem ���̓t�@�C���A�o�̓t�@�C���A�J�n�N�A�V�~�����[�g�N�����w�肷��B
echo ----- cligen53�R�}���h�J�n
%COMMAND3% -i%STATION_ID%.par -o%STATION_ID%.out -b%START_YEAR% -y%NUMBER_OF_YEARS% -t5 >> cligen53.log
echo ----- cligen53�R�}���h�I��

endlocal
